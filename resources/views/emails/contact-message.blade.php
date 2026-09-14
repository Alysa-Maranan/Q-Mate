<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>New Contact Message</title>
</head>
<body style="margin:0;padding:0;background:#f0fdf4;font-family:'Segoe UI',Arial,sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f0fdf4;padding:32px 16px;">
        <tr>
            <td align="center">
                <table width="100%" style="max-width:560px;background:#fff;border-radius:16px;border:2px solid #d1fae5;overflow:hidden;box-shadow:0 4px 24px rgba(16,185,129,0.1);">

                    <!-- Header -->
                    <tr>
                        <td style="background:linear-gradient(135deg,#10b981,#059669);padding:28px 32px;text-align:center;">
                            <div style="font-size:2.5rem;margin-bottom:8px;">📬</div>
                            <h1 style="color:#fff;font-size:1.375rem;font-weight:700;margin:0 0 4px 0;">New Contact Message</h1>
                            <p style="color:#d1fae5;font-size:0.875rem;margin:0;">SQUIFM — Smart Quail Feeder Management</p>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding:28px 32px;">

                            <p style="color:#374151;font-size:0.9375rem;margin:0 0 20px 0;">
                                Isang bagong mensahe ang natanggap mula sa Contact Support form.
                            </p>

                            <!-- Sender Info -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="background:#f9fafb;border:1.5px solid #e5e7eb;border-radius:10px;overflow:hidden;margin-bottom:20px;">
                                <tr>
                                    <td style="padding:14px 18px;border-bottom:1px solid #e5e7eb;">
                                        <span style="font-size:0.75rem;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:0.05em;">From</span><br>
                                        <span style="font-size:1rem;font-weight:700;color:#047857;">{{ $contactMessage->fullname }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:14px 18px;border-bottom:1px solid #e5e7eb;">
                                        <span style="font-size:0.75rem;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:0.05em;">Email</span><br>
                                        <a href="mailto:{{ $contactMessage->email }}" style="font-size:0.9375rem;color:#2563eb;text-decoration:none;">{{ $contactMessage->email }}</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:14px 18px;border-bottom:1px solid #e5e7eb;">
                                        <span style="font-size:0.75rem;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:0.05em;">Subject</span><br>
                                        <span style="font-size:0.9375rem;font-weight:600;color:#374151;">{{ $contactMessage->subject }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:14px 18px;">
                                        <span style="font-size:0.75rem;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:0.05em;">Received</span><br>
                                        <span style="font-size:0.9375rem;color:#374151;">{{ $contactMessage->created_at->format('F d, Y h:i A') }}</span>
                                    </td>
                                </tr>
                            </table>

                            <!-- Message -->
                            <div style="background:#f0fdf4;border:1.5px solid #6ee7b7;border-radius:10px;padding:18px 20px;margin-bottom:24px;">
                                <span style="font-size:0.75rem;font-weight:600;color:#059669;text-transform:uppercase;letter-spacing:0.05em;display:block;margin-bottom:10px;">Message</span>
                                <p style="font-size:0.9375rem;color:#1f2937;margin:0;line-height:1.7;white-space:pre-wrap;">{{ $contactMessage->message }}</p>
                            </div>

                            <!-- Reply Button -->
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center">
                                        <a href="mailto:{{ $contactMessage->email }}?subject=Re: {{ rawurlencode($contactMessage->subject) }}"
                                           style="display:inline-block;padding:12px 28px;background:#10b981;color:#fff;font-size:0.9375rem;font-weight:700;border-radius:10px;text-decoration:none;">
                                            ✉️ Reply to {{ $contactMessage->fullname }}
                                        </a>
                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background:#f9fafb;border-top:1.5px solid #e5e7eb;padding:16px 32px;text-align:center;">
                            <p style="font-size:0.75rem;color:#9ca3af;margin:0;">
                                This email was automatically sent by SQUIFM.<br>
                                You can also view this message in your
                                <a href="{{ url('/contact-inbox') }}" style="color:#10b981;text-decoration:none;">Admin Inbox</a>.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>
</html>
