<?php


namespace Modules\Beneficiaries\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\URL;
use Modules\Beneficiaries\Models\Beneficiary;

/**
 * Service class for managing Beneficiaries.
 *
 * This class isolates the business logic for Beneficiaries, handling:
 * 1. Database Interactions (CRUD).
 */
class BeneficiaryService
{

    /**
     * Cache Time-To-Live: 1 Hour (in seconds).
     */
    private const CACHE_TTL = 3600;

    /**
     * Centralized Cache Tags.
     * Defined as constants to prevent hardcoded string typos.
     */
    private const TAG_BENEFICIARIES_GLOBAL = 'beneficiaries';     // Tag for lists of beneficiaries
    private const TAG_BENEFICIARY_PREFIX = 'beneficiary_';      // Tag for specific beneficiary details

    /**
     * List Beneficiaries with a high-performance Caching Strategy.
     *
     * This method retrieves a paginated list of beneficiaries while ensuring that
     * database load is minimized through a tagged cache system. It handles dynamic filtering.
     *
     * Key Logic:
     * - Parameter Normalization: Uses `ksort` so that the order of query parameters 
     * does not result in duplicate cache entries (Cache Hit Optimization).
     * - Cache Key Integrity: Generates a MD5 signature based on filters, pagination, 
     * and limit to ensure data consistency.
     * - Tag-based Invalidation: Uses a global tag to allow instantaneous clearing 
     * of all list results when underlying data changes.
     *
     * @param array<string, mixed> $filters Associative array of active filters (e.g., governorate, gender).
     * @param int $perPage Number of records per page for pagination.
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function list(array $filters = [], int $perPage = 5): LengthAwarePaginator
    {
        // 1. Normalize Filters:
        // We sort by key so that requests like ?gender=male&page=1
        // and ?page=1&gender=male generate the SAME cache key.
        ksort($filters);

        // 2. Pagination State:
        // Retrieve the current page number from the request to include it in the cache key.
        $page = (int) request('page', 1);

        // 3. Unique Cache Key Generation:
        // Hash the serialized parameters to create a safe, short, and unique cache key.
        $cacheBase = json_encode($filters) . "_limit_{$perPage}_page_{$page}";
        $cacheKey = 'beneficiaries_list_' . md5($cacheBase);

        // 4. Atomic Cache Retrieval & Storage:
        // Uses the TAG_BENEFICIARIES_GLOBAL to facilitate the Ripple Effect invalidation strategy.
        return Cache::tags([self::TAG_BENEFICIARIES_GLOBAL])->remember(
            $cacheKey,
            self::CACHE_TTL,
            function () use ($filters, $perPage) {
                return Beneficiary::query()
                    ->filter($filters)      // Executes the specialized BeneficiaryBuilder orchestration.
                    ->paginate($perPage);   // Returns a paginated instance with metadata.
            }
        );
    }

    /**
     * Retrieve a specific beneficiary by ID with a dual-tag caching strategy.
     * 
     * Workflow:
     * 1. Check cache using a unique key and two specific tags:
     * - Global Tag: To invalidate all beneficiaries at once.
     * - Individual Tag: To invalidate only this specific beneficiary record.
     * 2. If not cached, fetch from database (respecting Global Scopes).
     * 3. Return the beneficiary instance or throw ModelNotFoundException.
     *
     * @param int $id The unique identifier of the beneficiary.
     * @return Beneficiary
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getById(int $id): Beneficiary
    {
        $cacheKey = self::TAG_BENEFICIARY_PREFIX . "details_{$id}";

        $beneficiaryTag = self::TAG_BENEFICIARY_PREFIX . $id;

        return Cache::tags([self::TAG_BENEFICIARIES_GLOBAL, $beneficiaryTag])->remember(
            $cacheKey,
            self::CACHE_TTL,
            fn() => Beneficiary::findOrFail($id)
        );
    }


    /**
     * Create a new beneficiary record and handle initial media upload.
     * 
     * Process:
     * 1. Inject the authenticated user ID and generate a custom system code.
     * 2. Persist the beneficiary record to the database.
     * 3. Process the identity file upload using Spatie MediaLibrary with custom naming.
     * 4. Attach a secure, temporary signed URL for the API response.
     *
     * @param array $data Validated input data containing beneficiary details and 'identity_file'.
     * @return Beneficiary
     */
    public function store(array $data): Beneficiary
    {
        // add the authenticated user to the validated data array ($data)
        $data['user_id'] = auth()->id();

        // call the `generateSystemCode` method and pass by reffernce the `$data` to add both `system_code` and `serial_number`
        self::generateSystemCode($data);

        $beneficiary = Beneficiary::create($data);

        // attach the identity to the current beneficiary
        // Note: here we don't check if the identity_file is set becasue it's required field in `StoreBeneficiaryRequest`
        $beneficiary->addMedia($data['identity_file'])
            ->usingFileName("identity_" . $beneficiary->system_code . "." . $data['identity_file']->extension())
            ->toMediaCollection('identities');

        // return the `$beneficiary` with temporary identity_url generated by `attachIdentityUrl` method
        return self::attachIdentityUrl($beneficiary);
    }

    /**
     * Update beneficiary details and optionally refresh the identity document.
     * 
     * * Process:
     * 1. Update the beneficiary's basic attributes.
     * 2. If a new 'identity_file' is provided, replace the existing media in the collection.
     * 3. Refresh the model instance to ensure all database-level changes are synced.
     * 4. Re-attach a fresh temporary signed URL for the updated resource.
     *
     * @param Beneficiary $beneficiary The existing model instance.
     * @param array $data Validated data for update.
     * @return Beneficiary The refreshed model with the new signed URL.
     */
    public function update(Beneficiary $beneficiary, array $data): Beneficiary
    {
        $beneficiary->update($data);

        // check if the `identity_file` set
        if (isset($data['identity_file'])) {
            $beneficiary->addMedia($data['identity_file'])
                ->usingFileName("identity_" . $beneficiary->system_code . "." . $data['identity_file']->extension())
                ->toMediaCollection('identities');
        }

        $beneficiary->refresh();

        return self::attachIdentityUrl($beneficiary);
    }

    /**
     * Delete a Beneficiary
     *
     * @param Beneficiary $beneficiary
     * @return void
     */
    public function delete(Beneficiary $beneficiary): void
    {
        $beneficiary->delete();
    }

    /**
     * Generate a unique system code and serial number for the beneficiary.
     * * Logic:
     * 1. Fetch the max serial number for the current year (including trashed records).
     * 2. Increment the serial number.
     * 3. Convert the serial number to Base36 (alphanumeric) and pad with zeros.
     * 4. Format the final code as: HOPE-{YY}-{SERIAL} (e.g., HOPE-26-00G).
     *
     * @param array $data Reference to the data array to be updated with the code.
     * @return void
     */
    private function generateSystemCode(&$data)
    {
        // 1. Get the current year
        $year = date('y');

        // 2. Get the last serial number including trashed recoreds to avoid repeated serials if a recored deleted
        $lastSerialNumber = Beneficiary::withTrashed()->whereYear('created_at', date('Y'))->max('serial_number');

        // 3. increase last serial number by one
        $newSerialNumber = ($lastSerialNumber ?? 0) + 1;

        // 4. store the new serial number
        $data['serial_number'] = $newSerialNumber;

        // 5. convert the serial number into alphanumeric and make it uppercase
        $base36 = strtoupper(base_convert($newSerialNumber, 10, 36));

        // 6. pad the converted serial number `$base36` with zeros
        $paddedSerial = str_pad($base36, 3, '0', STR_PAD_LEFT);

        // 7. store the final system code in referenced `$data` like: HOPE-{YY}-{SERIAL} (e.g., HOPE-26-00G).
        $data['system_code'] = "HOPE-{$year}-{$paddedSerial}";
    }

    /**
     * Attach a temporary signed URL for the beneficiary's identity file.
     * * This method:
     * 1. Generates a secure, time-limited signed route (valid for 15 minutes).
     * 2. Dynamically injects the 'identity_url' attribute into the model object.
     * 3. Unsets the 'media' relationship to keep the API response clean and concise.
     *
     * @param Beneficiary $beneficiary The beneficiary model instance.
     * @return Beneficiary The model instance with the appended attribute.
     */
    private function attachIdentityUrl(Beneficiary $beneficiary): Beneficiary
    {
        // 1. make a temporary url for beneficiary's identity (valid for 15 minutes)
        $url = URL::temporarySignedRoute(
            'api.beneficiaries.identity',
            now()->addMinutes(15),
            ['beneficiary' => $beneficiary->id]
        );

        // 2. add `identity_url` to the beneficiary
        $beneficiary->setAttribute('identity_url', $url);

        // 3. unset media relationship so the intire media object does not appear when updating
        $beneficiary->unsetRelation('media');

        return $beneficiary;
    }
}
