<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document Signature Request</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f5f5f5;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f5f5f5; padding: 20px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                    
                    <!-- Header with Logo -->
                    <tr>
                        <td style="background-color: #3366CC; padding: 30px 40px; text-align: center;">
                            <!-- Logo -->
                            <img src="https://bansalcrm.com/assets/images/bansal-logo-white.png" alt="Bansal Immigration" style="height: 60px; margin-bottom: 15px;" onerror="this.style.display='none'">
                            <h1 style="color: #ffffff; margin: 0; font-size: 24px; font-weight: 600;">Document Signature Request</h1>
                            <p style="color: #cce0ff; margin: 10px 0 0 0; font-size: 14px;">Bansal Migration Immigration & Visa Services</p>
                        </td>
                    </tr>
                    
                    <!-- Content -->
                    <tr>
                        <td style="padding: 40px;">
                            <p style="font-size: 16px; color: #333; margin: 0 0 20px 0;">
                                <strong>Dear {{ $signerName }},</strong>
                            </p>
                            
                            <p style="font-size: 15px; color: #555; line-height: 1.6; margin: 0 0 25px 0;">
                                Please review and sign the attached document.
                            </p>
                            
                            <!-- Document Details Box -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 30px;">
                                <tr>
                                    <td style="background-color: #f8f9fa; border-left: 4px solid #3366CC; padding: 20px; border-radius: 4px;">
                                        <p style="margin: 0 0 10px 0; font-size: 16px; font-weight: 600; color: #333;">Document Details</p>
                                        <p style="margin: 0 0 5px 0; font-size: 14px; color: #555;">
                                            <strong>Document:</strong> {{ $documentTitle }}
                                        </p>
                                        <p style="margin: 0; font-size: 14px; color: #555;">
                                            <strong>Type:</strong> Document
                                        </p>
                                    </td>
                                </tr>
                            </table>
                            
                            <!-- Sign Button -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 25px;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ $signingUrl }}" style="display: inline-block; background-color: #3366CC; color: #ffffff; text-decoration: none; padding: 16px 50px; border-radius: 6px; font-weight: 600; font-size: 16px;">Review & Sign Document</a>
                                    </td>
                                </tr>
                            </table>
                            
                            <!-- Important Notice -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 25px;">
                                <tr>
                                    <td style="background-color: #fff8e6; border-left: 4px solid #f5a623; padding: 15px; border-radius: 4px;">
                                        <p style="margin: 0; font-size: 14px; color: #333;">
                                            <strong>Important:</strong> This link is unique to you and should not be shared. It will expire once the document is signed or after the due date.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                            
                            <p style="font-size: 14px; color: #555; line-height: 1.6; margin: 0 0 15px 0;">
                                If you have any questions or need assistance, please don't hesitate to contact us.
                            </p>
                            
                            <p style="font-size: 14px; color: #555; line-height: 1.6; margin: 0 0 25px 0;">
                                Thank you for your prompt attention to this matter.
                            </p>
                            
                            <!-- Signature -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="border-top: 1px solid #eee; padding-top: 20px;">
                                <tr>
                                    <td>
                                        <p style="margin: 0 0 5px 0; font-size: 14px; color: #333;"><strong>Regards,</strong></p>
                                        <p style="margin: 0; font-size: 14px; color: #333;"><strong>Bansal Migration Team</strong></p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8f9fa; padding: 25px 40px; text-align: center; border-top: 1px solid #e9ecef;">
                            <p style="margin: 0 0 5px 0; font-size: 14px; font-weight: 600; color: #333;">Bansal Migration</p>
                            <p style="margin: 0; font-size: 13px; color: #666;">Immigration & Visa Services</p>
                        </td>
                    </tr>
                    
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
