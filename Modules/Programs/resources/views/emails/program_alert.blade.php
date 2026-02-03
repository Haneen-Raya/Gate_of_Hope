<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #202124; margin: 0; padding: 0; background-color: #f1f3f4; }
        .wrapper { padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .header { background-color: #1a73e8; color: #ffffff; padding: 30px; text-align: center; }
        .content { padding: 40px; }
        .alert-box { border-left: 4px solid #d93025; background-color: #fce8e6; padding: 20px; margin: 20px 0; border-radius: 0 4px 4px 0; }
        .table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .table td { padding: 10px 0; border-bottom: 1px solid #eee; font-size: 14px; }
        .label { font-weight: bold; color: #5f6368; width: 40%; }
        .footer { padding: 20px; text-align: center; font-size: 12px; color: #70757a; background-color: #f8f9fa; }
        .btn { display: inline-block; padding: 12px 24px; background-color: #1a73e8; color: #ffffff; text-decoration: none; border-radius: 4px; font-weight: 500; margin-top: 25px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <div class="header"><h1>Hope Gate Alert</h1></div>
            <div class="content">
                <h3>Dear Program Manager,</h3>
                <p>An automated audit has identified a critical update regarding one of your programs. Please find the details below:</p>

                <div class="alert-box">
                    <strong>Current Alert:</strong> {{ $details['title'] }}
                </div>

                <table class="table">
                    <tr><td class="label">Program Name</td><td>{{ $details['program_name'] }}</td></tr>
                    <tr><td class="label">Resource Impacted</td><td>{{ $details['resource_name'] }}</td></tr>
                    <tr><td class="label">Updated Balance</td><td><strong>{{ $details['quantity'] }} Units</strong></td></tr>
                    <tr><td class="label">Last Action By</td><td>{{ auth()->user()->name ?? 'System' }}</td></tr>
                </table>

                <p style="margin-top: 20px;">{{ $details['body'] }}</p>

                <div style="text-align: center;">
                    <a href="{{ url('/programs/' . $details['program_id']) }}" class="btn">Review Dashboard</a>
                </div>
            </div>
            <div class="footer">Automated System Notification &copy; {{ date('Y') }} Hope Gate</div>
        </div>
    </div>
</body>
</html>
