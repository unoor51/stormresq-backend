@component('mail::message')

<div style="text-align: center; margin-bottom: 20px;"> <img src="http://127.0.0.1:8000/images/stormresq-logo.png" alt="StormResQ Logo" style="height: 60px;"> </div>
🚨 New Rescuer Registered
A new rescuer has just registered on the platform. Below are the details:

@component('mail::panel')

🧑 Name: {{ $rescuer->first_name }} {{ $rescuer->last_name }}

📧 Email: {{ $rescuer->email }}

📞 Phone: {{ $rescuer->phone }}
@endcomponent

<a href="{{ url('/admin/rescuers') }}" style="display: inline-block; background-color: #f97316; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;"> 🔍 View Rescuers </a>

Thanks,
StormResQ

@endcomponent