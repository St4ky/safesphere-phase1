<?php
// Deepfake & AI Voice/Video Scams Data (Indian Context)
// Real scenarios: WhatsApp Voice Clones, Virtual Kidnapping, CEO Urgent Transfer, Fake Police Video Call

return [
    [
        'id' => 'df1',
        'title' => 'Virtual Arrest / Fake Police Video Call',
        'threat_type' => 'Deepfake Video & Digital Arrest',
        'urgency' => 'High',
        'audio_transcript' => "Video caller in Delhi Police uniform sitting in front of a police emblem:\n\n'Mr. Sharma, your Aadhaar card #XXXX-1928 has been found attached to a contraband FedEx parcel containing 16 passports and synthetic drugs detained at Mumbai customs. A money laundering case has been registered under PMLA.\n\nYou are placed under Digital Arrest right now on this video call. Do not turn off your camera or inform your family. To verify your innocence, you must immediately transfer all your liquid bank funds to the RBI Government Monitoring Escrow Account for financial clearance verification.'",
        'media_indicators' => [
            'Caller appears on video call in a full police uniform with background badges',
            'Unnatural eye blinking rate, audio slightly out of sync with lip movements',
            'Threatens "Digital Arrest" — an invented term with no legal validity under Indian law'
        ],
        'is_scam' => true,
        'options' => [
            'transfer_escrow' => 'Transfer money to the RBI Escrow account to clear your name',
            'disconnect_1930' => 'Disconnect immediately, report to National Cybercrime Helpline 1930 and local police station',
            'plead_guilty' => 'Beg for mercy and ask to settle with a smaller bribe'
        ],
        'correct_option' => 'disconnect_1930',
        'explanation' => "IMPORTANT: There is NO provision for 'Digital Arrest' in the Indian Penal Code or CrPC. Neither CBI, Police, nor Customs conducts interrogations or arrests via Skype/WhatsApp video calls, nor do they ever demand fund transfers to 'verification accounts'.",
        'safety_tips' => [
            'Hang up immediately on any caller threatening "Digital Arrest".',
            'Indian law enforcement officers will never ask you to transfer funds to prove innocence.',
            'Report the phone number immediately to 1930 or cybercrime.gov.in.'
        ]
    ],
    [
        'id' => 'df2',
        'title' => 'AI Voice Clone of Child / Family Member',
        'threat_type' => 'AI Voice Synthesis & Emergency Extortion',
        'urgency' => 'Extreme',
        'audio_transcript' => "Sobbing Voice (Sounds remarkably identical to your college-going son Aryan):\n\n'Papa... Papa please help me! I met with an accident near the highway. The other driver is beating me and holding me hostage. Police are taking me away... please send ₹75,000 to this Google Pay number right now or they will break my legs... please papa hurry up!'\n\nCall transfers to a harsh aggressive voice:\n'Listen carefully. Transfer ₹75,000 to this UPI ID in 10 minutes or your son goes to jail!'",
        'media_indicators' => [
            'Voice tone, pitch, and colloquial dialect sound 95% identical to your real son',
            'Caller demands immediate UPI transfer without allowing you to hang up or call back',
            'High emotional hijacking designed to bypass critical thinking'
        ],
        'is_scam' => true,
        'options' => [
            'pay_immediately' => 'Immediately send ₹75,000 via UPI to rescue your child',
            'verify_family_code' => 'Keep calm, call son\'s direct phone number or his roommate/hostel warden from a second phone',
            'bargain' => 'Bargain to pay ₹25,000 right now'
        ],
        'correct_option' => 'verify_family_code',
        'explanation' => "Scammers use brief audio samples from social media reels/stories (Instagram, YouTube) to clone voices using AI generators like ElevenLabs. Always independently reach the person or verify with a secret family codeword before acting.",
        'safety_tips' => [
            'Establish a confidential "Family Safe Word" known only to immediate family members for emergency verification.',
            'Never panic. Call the family member directly on their known phone or contact their hostel/friends.',
            'Do not send money under emergency pressure without independent physical verification.'
        ]
    ],
    [
        'id' => 'df3',
        'title' => 'Urgent WhatsApp Audio from Company CEO',
        'threat_type' => 'Executive Voice Impersonation (CEO Fraud)',
        'urgency' => 'High',
        'audio_transcript' => "WhatsApp Voice Note from CEO's display profile:\n\n'Hi Vikram, I am in a confidential board meeting with investors and cannot take calls. We need to close an urgent vendor acquisition contract before market closes today. Please release a priority RTGS of ₹12,50,000 to the attached vendor account immediately. Do not discuss this with the team yet due to NDA. I will sign the PO tomorrow.'",
        'media_indicators' => [
            'Voice note matches CEO\'s exact timbre, pace, and vocal cadence',
            'Message asks to bypass standard finance approval workflows and procurement PO process',
            'Enforces secrecy under the guise of an NDA'
        ],
        'is_scam' => true,
        'options' => [
            'execute_transfer' => 'Execute the RTGS immediately as requested by executive authority',
            'out_of_band_verify' => 'Follow standard dual-authorization financial procedure and verify in person or via official company Slack/Teams',
            'refuse_rudely' => 'Send back a rude reply questioning his identity'
        ],
        'correct_option' => 'out_of_band_verify',
        'explanation' => "Known as 'CEO Fraud' or Business Email/Voice Compromise. Attackers use publicly available interviews of executives to synthesize realistic voice instructions. Standard dual-signoff policies must NEVER be bypassed for any phone/voice request.",
        'safety_tips' => [
            'Always verify unexpected payment requests through an official secondary channel (Slack, office landline, or dual signatory approval).',
            'Urgent requests to bypass approval policies under secrecy are classic corporate social engineering.'
        ]
    ],
    [
        'id' => 'df4',
        'title' => 'Genuine Automated Bank OTP Call',
        'threat_type' => 'Automated Interactive Voice Response (IVR)',
        'urgency' => 'Normal',
        'audio_transcript' => "Automated Voice:\n'Dear HDFC customer, you have initiated net banking login from a new Chrome browser. If this was you, please press 1. If you did not initiate this request, press 2 to immediately block your NetBanking credentials. Note: This automated system will never ask you to speak your PIN, password, or OTP.'",
        'media_indicators' => [
            'Standard neutral IVR computer voice, clear robotic cadence',
            'Does NOT ask for passwords, OTPs, or CVV',
            'Provides a safe one-key action to block compromised credentials'
        ],
        'is_scam' => false,
        'options' => [
            'press_option' => 'Press 1 if you just logged in, or Press 2 to freeze account if you did not',
            'give_pin' => 'Speak your ATM PIN clearly into the microphone',
            'threaten_lawsuit' => 'Shout at the automated machine'
        ],
        'correct_option' => 'press_option',
        'explanation' => "This is a legitimate automated fraud alert IVR. It explicitly cautions you that it will never request confidential data and offers a one-touch defensive block mechanism.",
        'safety_tips' => [
            'Notice that legitimate security systems inform and protect, without asking for credentials or demanding wire transfers.'
        ]
    ]
];
