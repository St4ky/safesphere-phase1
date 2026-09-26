<?php
// OTP & Voice Scam Simulation Scenarios (Indian Context)
// Includes SMS OTP theft, Bank caller Vishing, Courier delivery code, WhatsApp verification hijacking, Electricity disconnection alert

return [
    [
        'id' => 'otp1',
        'type' => 'voice',
        'caller_id' => '+91 98210 44321',
        'caller_name' => 'State Bank Card Support (Suspected)',
        'channel' => 'Incoming Voice Call',
        'time' => '10:24 AM',
        'dialogue' => "Agent: 'Good morning sir, this is Rajesh calling from your bank credit card division. We noticed an unauthorized transaction of ₹34,999 on Amazon from a computer in Kolkata. We have placed it on temporary hold.\n\nTo cancel this payment and block future attempts, we are initiating a security verification. I have just triggered a cancellation OTP to your registered phone. Please read the 6-digit cancellation code so I can reverse the charge immediately.'",
        'sms_popup' => [
            'sender' => 'VM-SBINB',
            'text' => 'OTP for transaction of INR 34,999.00 at AMAZON PAY is 782419. Do not share OTP with anyone, including bank staff.'
        ],
        'is_scam' => true,
        'options' => [
            'share' => 'Share the 6-digit code to cancel transaction',
            'refuse' => 'Hang up immediately and call official bank customer care',
            'ask_mgr' => 'Ask for his employee ID and manager phone number'
        ],
        'correct_option' => 'refuse',
        'explanation' => "CRITICAL RULE: Bank representatives never call to ask for an OTP. The SMS explicitly stated: 'OTP for transaction of INR 34,999.00'. Sharing this OTP completes the theft, it does not cancel it.",
        'red_flags' => [
            'The caller claims the OTP is for "cancellation" — OTPs authorize payments, never cancel them.',
            'The SMS content says "OTP for transaction", revealing the scammer already had the card details and was waiting for the OTP.',
            'Manufactured panic and urgency ("unauthorized transaction in Kolkata").'
        ]
    ],
    [
        'id' => 'otp2',
        'type' => 'sms',
        'caller_id' => 'VK-ELECTR',
        'caller_name' => 'State Electricity Board',
        'channel' => 'SMS Alert',
        'time' => '07:15 PM',
        'dialogue' => "SMS Received:\n'Dear Consumer, Your electricity power will be disconnected tonight at 9:30 PM from the electricity office because your previous month bill was not updated. Please immediately contact our power officer at 98451-20981. Provide the verification code sent to your phone to avoid disconnection.'",
        'sms_popup' => [
            'sender' => 'VK-ELECTR',
            'text' => 'Your one-time verification passcode is 319082 for Quick Electricity Account link.'
        ],
        'is_scam' => true,
        'options' => [
            'call_and_share' => 'Call the number and share passcode to stop disconnection',
            'pay_online' => 'Check official electricity board portal/app directly',
            'forward_otp' => 'Reply to the SMS with the OTP'
        ],
        'correct_option' => 'pay_online',
        'explanation' => "The 'Electricity Disconnection Scam' is widespread across India. Utility providers never send personal mobile numbers for bill clearance, nor do they disconnect power abruptly at night without statutory notices.",
        'red_flags' => [
            'Personal 10-digit mobile number provided instead of official customer support.',
            'Intense time pressure ("tonight at 9:30 PM").',
            'Asks for a verification passcode to prevent a service interruption.'
        ]
    ],
    [
        'id' => 'otp3',
        'type' => 'delivery',
        'caller_id' => '+91 97112 00192',
        'caller_name' => 'Courier Delivery Executive',
        'channel' => 'Phone Call & SMS',
        'time' => '02:40 PM',
        'dialogue' => "Delivery Boy: 'Sir, I have your parcel from BlueDart / Amazon. I am standing near your gate. To confirm delivery and hand over the parcel, please share the delivery PIN sent to your mobile.'\n\nYou are indeed expecting a parcel you ordered two days ago.",
        'sms_popup' => [
            'sender' => 'AX-BLDRT',
            'text' => 'Your package #BL78120 is out for delivery. Share Delivery PIN 4419 with courier agent upon receiving package.'
        ],
        'is_scam' => false,
        'options' => [
            'share_after_inspect' => 'Verify package address and give Delivery PIN at doorstep',
            'refuse' => 'Never share any PIN under any circumstances',
            'pay_upi' => 'Ask him if any extra charge is needed via UPI'
        ],
        'correct_option' => 'share_after_inspect',
        'explanation' => "This is a legitimate Delivery Confirmation PIN. E-commerce couriers (Amazon, Flipkart, BlueDart) routinely use a Delivery PIN to ensure packages reach the correct recipient.",
        'red_flags' => [
            'SMS text specifically says "Delivery PIN" and not "Bank Transaction OTP".',
            'No financial request or debit occurred.',
            'Delivery matches a parcel you genuinely placed and the courier is physically at your doorstep.'
        ]
    ],
    [
        'id' => 'otp4',
        'type' => 'whatsapp',
        'caller_id' => '+91 91234 56789',
        'caller_name' => 'Old College Friend (Hacked)',
        'channel' => 'WhatsApp Chat',
        'time' => '11:15 PM',
        'dialogue' => "Friend: 'Hey bro! Super urgent please help. I am locked out of my phone and accidentally sent my 6-digit WhatsApp registration code to your number. Can you please forward that SMS code to me right now? My phone is resetting!'",
        'sms_popup' => [
            'sender' => 'WhatsApp',
            'text' => 'Your WhatsApp code: 902-148. Do not share this code with others.'
        ],
        'is_scam' => true,
        'options' => [
            'forward' => 'Copy and send the 6-digit code to your friend',
            'call_direct' => 'Call your friend on normal mobile call to verify',
            'ignore' => 'Ignore and block the contact'
        ],
        'correct_option' => 'call_direct',
        'explanation' => "This is the classic WhatsApp Account Takeover Scam. The scammer entered YOUR phone number on a new device to hijack your account, and is pretending to be a friend so you hand over your own WhatsApp verification code.",
        'red_flags' => [
            'WhatsApp verification codes are tied to the SIM number that receives them, never sent to someone else by mistake.',
            'Giving this code surrenders your WhatsApp account to the attacker immediately.'
        ]
    ],
    [
        'id' => 'otp5',
        'type' => 'sim_swap',
        'caller_id' => '+91 98100 99882',
        'caller_name' => 'Telecom 5G Upgrade Support',
        'channel' => 'Voice Call',
        'time' => '04:10 PM',
        'dialogue' => "Caller: 'Namaste sir, this is Airtel/Jio network office. 3G/4G services in your area are shutting down today. We need to migrate your SIM to 5G Plus. Please SMS 'SIM 8991200381928' to 121 and provide the confirmation OTP to keep your outgoing calls active.'",
        'sms_popup' => [
            'sender' => 'AD-AIRTEL',
            'text' => 'Request received for SIM swap/eSIM replacement. If not requested by you, call 198 immediately.'
        ],
        'is_scam' => true,
        'options' => [
            'send_and_confirm' => 'Send the SMS and confirm code to upgrade to 5G',
            'reject_and_report' => 'Reject call immediately, do NOT send SMS, visit official store',
            'give_aadhar' => 'Offer your Aadhaar number instead'
        ],
        'correct_option' => 'reject_and_report',
        'explanation' => "This is a SIM Swap Attack. Sending that 20-digit SIM number to telecom shortcodes instructs your provider to transfer your phone number to the scammer's blank SIM card. Once swapped, all your banking OTPs go directly to the attacker.",
        'red_flags' => [
            'Telecom companies never require you to send personal SIM replacement codes over phone calls.',
            'The SMS warning states "Request received for SIM swap/eSIM replacement".',
            'A swapped SIM causes total loss of cellular signal and leaves all your financial accounts vulnerable.'
        ]
    ]
];
