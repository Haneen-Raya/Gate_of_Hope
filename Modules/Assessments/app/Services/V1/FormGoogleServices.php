<?php

namespace Modules\Assessments\Services\V1;

use Illuminate\Support\Facades\Cache;
use Modules\Assessments\Models\GoogleForm;

    class FormGoogleServices {

        /**
         * Cache Time-To-Live : 1 Hour .
         * @var int
         */
        private const CACHE_TTL = 3600;
        /**
         * Cache Tags to prevent typos and centralize invalidation.
         * @var string
         */
        private const TAG_FORMS_GLOBAL = 'google_forms';

        private const TAG_FORM_PREFIX  = 'google_form_';

        public function list( int $perpage = 10){
            $page = (int) request('page',1);
            $cachekey = "form_list_P{$page}_limit_{$perpage}";
                return Cache::tags([self::TAG_FORMS_GLOBAL])->remember($cachekey, self::CACHE_TTL,
            fn() => GoogleForm::with('issueType')->paginate($perpage)
                );
        }

        /**
         *
         * @param int $id
         * @return GoogleForm
         */
        public function getById(int $id){
            $cachekey    = self::TAG_FORM_PREFIX . "details_{$id}";
            $specificTag = self::TAG_FORM_PREFIX . $id ;

            return Cache::tags([self::TAG_FORMS_GLOBAL,$specificTag])
                ->remember($cachekey , self::CACHE_TTL,
                fn()=> GoogleForm::with('issueType')->findOrFail($id)
                );
        }

        /**
         *
         * @param array $data
         * @return GoogleForm
         */
        public function create(array $data){
            $form = GoogleForm::create($data);
            $this->clearGlobalCache();
            return $form ;
        }

        /**
         *
         * @param int $id
         * @param array $data
         * @return GoogleForm
         */
        public function update(int $id , array $data ){
            $form = GoogleForm::findOrFail($id);
            $form->update($data);

            $this->clearSpecificCache($id);
            return $form ;
        }

        /**
         *
         * @param int $id
         */
        public function delete(int $id){
            $form = GoogleForm::findOrFail($id);
            $this->clearSpecificCache($id);
            return $form->delete();
        }

        /**
         *
         * @param int $id
         * @return void
         */
        public function clearSpecificCache(int $id){
            Cache::tags([self::TAG_FORM_PREFIX . $id,self::TAG_FORMS_GLOBAL]);
        }

        /**
         * 
         * @return void
         */
        public function clearGlobalCache(){
            Cache::tags([self::TAG_FORMS_GLOBAL])->flush();
        }
}
?>
