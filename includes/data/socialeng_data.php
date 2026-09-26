<?php
// SafeSphere — Social Engineering Chat Scenarios
// 4 branching conversation trees.
// Each scenario has: id, title, icon, description, persona (the attacker),
//   and 'nodes' (chat turns). Each node has:
//     id, messages (array of attacker messages), choices (array: [label, next_node, is_correct, points])
// Terminal nodes have no choices — just messages + outcome.

return [
    [
        'id'          => 'se_01',
        'icon'        => '🏦',
        'title'       => 'Fake Bank KYC Call',
        'description' => 'You receive a message from someone claiming to be from your bank. They say your account will be suspended unless you complete KYC re-verification.',
        'persona'     => [
            'name'   => 'KYC Support — Axis Bank',
            'avatar' => 'AX',
            'color'  => '#dc2626',
        ],
        'nodes' => [
            'start' => [
                'messages' => [
                    "Dear Customer, your Axis Bank account ending 4821 is due for mandatory KYC re-verification as per RBI circular.",
                    "Your account will be temporarily suspended within 2 hours if KYC is not updated. Please cooperate to avoid inconvenience.",
                    "I am Rajesh from Axis Bank Customer Care. May I have your registered mobile number to proceed?"
                ],
                'choices' => [
                    ['label' => 'Give my mobile number to proceed', 'next' => 'gave_mobile', 'correct' => false, 'points' => -5],
                    ['label' => 'Ask for their employee ID and official number first', 'next' => 'asked_id', 'correct' => true, 'points' => 10],
                    ['label' => 'Tell them I\'ll call the bank\'s official number myself', 'next' => 'said_callback', 'correct' => true, 'points' => 10],
                ],
            ],
            'gave_mobile' => [
                'messages' => [
                    "Thank you. Now I am sending an OTP to your mobile for verification. Please share the OTP with me once you receive it."
                ],
                'choices' => [
                    ['label' => 'Share the OTP — it\'s for KYC verification', 'next' => 'gave_otp', 'correct' => false, 'points' => -5],
                    ['label' => 'Refuse to share OTP — banks never ask for OTPs over phone', 'next' => 'refused_otp', 'correct' => true, 'points' => 10],
                ],
            ],
            'gave_otp' => [
                'messages' => [
                    "⚠️ SCAM COMPLETE. By giving your OTP, you've allowed the attacker to log into your banking app and transfer funds.",
                    "Real bank staff NEVER ask for OTPs, PINs, or passwords — for any reason. The OTP they triggered was a transaction auth or login OTP.",
                    "You should: (1) Call your bank immediately to freeze the account, (2) Change your MPIN and app password, (3) File a complaint at cybercrime.gov.in"
                ],
                'choices' => [],
                'outcome' => 'fail',
            ],
            'refused_otp' => [
                'messages' => [
                    "✅ Excellent! You refused to share the OTP — this is the right call.",
                    "The caller becomes aggressive: 'Sir, this is mandatory. Your account will be blocked. Please cooperate.'",
                    "What do you do next?"
                ],
                'choices' => [
                    ['label' => 'They\'re from the bank — I should cooperate and share the OTP', 'next' => 'gave_otp', 'correct' => false, 'points' => -5],
                    ['label' => 'Hang up and call Axis Bank\'s official helpline: 1860-419-5555', 'next' => 'safe_end', 'correct' => true, 'points' => 10],
                ],
            ],
            'asked_id' => [
                'messages' => [
                    "My employee ID is AX-2947. You can verify on our official portal. Now, may I have your mobile number?",
                    "Note: Scammers can make up any ID — this doesn't verify legitimacy. You cannot verify an 'employee ID' given verbally."
                ],
                'choices' => [
                    ['label' => 'That sounds official — proceed with the KYC', 'next' => 'gave_mobile', 'correct' => false, 'points' => -5],
                    ['label' => 'Hang up and call Axis Bank directly on their official number', 'next' => 'safe_end', 'correct' => true, 'points' => 10],
                ],
            ],
            'said_callback' => [
                'messages' => [
                    "Sir, I cannot give you a callback number for security reasons. Please cooperate now — your account is at risk.",
                    "This response is a red flag. Legitimate bank representatives welcome you calling back on official numbers."
                ],
                'choices' => [
                    ['label' => 'OK, they seem urgent — maybe I should just cooperate', 'next' => 'gave_mobile', 'correct' => false, 'points' => -5],
                    ['label' => 'Hang up — a real bank rep would give me an official callback number', 'next' => 'safe_end', 'correct' => true, 'points' => 10],
                ],
            ],
            'safe_end' => [
                'messages' => [
                    "🛡️ You handled this perfectly! You identified the scam before sharing any personal information.",
                    "Key takeaways: Banks never call to ask for OTPs or passwords. Always verify by calling the official number on the back of your card. Urgency + unsolicited call = red flag.",
                    "In India, bank KYC is done through the official app, at the branch, or via a verified video KYC — never over a phone call from an unknown number."
                ],
                'choices' => [],
                'outcome' => 'pass',
            ],
        ],
    ],

    [
        'id'          => 'se_02',
        'icon'        => '💼',
        'title'       => 'Fake HR Job Offer',
        'description' => 'You see a job posting on LinkedIn and receive a message from an "HR manager" about an exciting opportunity with a great salary package.',
        'persona'     => [
            'name'   => 'Priya Mehta — TCS HR Recruiter',
            'avatar' => 'TCS',
            'color'  => '#7c3aed',
        ],
        'nodes' => [
            'start' => [
                'messages' => [
                    "Hi! I'm Priya Mehta, Senior HR at TCS. I found your profile on LinkedIn — very impressive background!",
                    "We have an urgent opening for Senior Software Engineer, ₹18LPA package + WFH. Interested?",
                    "The selection process is quick — just a 20-minute online test today and offer letter by tomorrow."
                ],
                'choices' => [
                    ['label' => "Yes! Tell me more — sounds great", 'next' => 'agreed_quickly', 'correct' => false, 'points' => -5],
                    ['label' => "Sounds interesting — let me verify your LinkedIn profile and TCS careers page first", 'next' => 'verified', 'correct' => true, 'points' => 10],
                    ['label' => "Ask for an official TCS email address and job posting reference", 'next' => 'asked_email', 'correct' => true, 'points' => 10],
                ],
            ],
            'agreed_quickly' => [
                'messages' => [
                    "Wonderful! To begin, we need to process your registration on our portal. There's a refundable processing fee of ₹2,999 — this covers your background check and appointment kit.",
                    "After joining you will get this back in your first month's salary."
                ],
                'choices' => [
                    ['label' => "Pay the fee — it\'s refundable and the job sounds worth it", 'next' => 'paid_fee', 'correct' => false, 'points' => -5],
                    ['label' => "Stop — legitimate companies NEVER charge candidates a fee", 'next' => 'refused_fee', 'correct' => true, 'points' => 10],
                ],
            ],
            'paid_fee' => [
                'messages' => [
                    "⚠️ JOB SCAM COMPLETE. Once you pay, the 'recruiter' blocks you and disappears.",
                    "This is a classic advance-fee job scam. TCS, Infosys, Wipro, and all legitimate companies NEVER charge fees for joining.",
                    "Red flags you missed: unsolicited message, no official email, urgency, vague 'portal,' and the fee request. Report such fraud at cybercrime.gov.in"
                ],
                'choices' => [],
                'outcome' => 'fail',
            ],
            'refused_fee' => [
                'messages' => [
                    "✅ Correct! Legitimate employers never charge candidates.",
                    "The 'recruiter' now claims: 'It's not a fee, it's a security deposit — all new hires pay it. Our HR policy requires this.'",
                ],
                'choices' => [
                    ['label' => "If it's company policy, maybe it's legitimate — I'll pay", 'next' => 'paid_fee', 'correct' => false, 'points' => -5],
                    ['label' => "Block and report — any charge to candidates is a scam, regardless of framing", 'next' => 'safe_end', 'correct' => true, 'points' => 10],
                ],
            ],
            'verified' => [
                'messages' => [
                    "Oh, the LinkedIn profile was just created recently. The TCS careers page lists a different process — no mention of this job posting.",
                    "The profile has only 12 connections and was created 3 days ago. The job posting URL provided leads to a suspicious non-TCS domain."
                ],
                'choices' => [
                    ['label' => "Maybe it's a new profile — I'll proceed anyway, the offer sounds too good to pass up", 'next' => 'agreed_quickly', 'correct' => false, 'points' => -5],
                    ['label' => "These are serious red flags — decline and report the profile to LinkedIn", 'next' => 'safe_end', 'correct' => true, 'points' => 10],
                ],
            ],
            'asked_email' => [
                'messages' => [
                    "Sure! My email is priya.mehta.tcs@gmail.com — I'm working from home today so using personal email.",
                    "The job reference is TCS-2024-SWE-URGENT. Apply directly by replying to me."
                ],
                'choices' => [
                    ['label' => "A Gmail for a TCS HR? That's acceptable for WFH — continue", 'next' => 'agreed_quickly', 'correct' => false, 'points' => -5],
                    ['label' => "TCS HR would use @tcs.com email — a Gmail is a clear red flag. Disengage.", 'next' => 'safe_end', 'correct' => true, 'points' => 10],
                ],
            ],
            'safe_end' => [
                'messages' => [
                    "🛡️ You avoided a job scam. Well done!",
                    "Key red flags in this scenario: Unsolicited approach, brand-new LinkedIn profile, fake company domain, unrealistic speed of hiring, fee demand.",
                    "Rule: Any job that asks for money from candidates is a scam. Always verify via official company careers pages and @company.com email addresses."
                ],
                'choices' => [],
                'outcome' => 'pass',
            ],
        ],
    ],

    [
        'id'          => 'se_03',
        'icon'        => '🖥️',
        'title'       => 'Tech Support Scammer',
        'description' => 'A pop-up on your browser says your computer has a virus and instructs you to call Microsoft support immediately. You call the number.',
        'persona'     => [
            'name'   => 'Microsoft Support — Agent Kevin',
            'avatar' => 'MS',
            'color'  => '#0369a1',
        ],
        'nodes' => [
            'start' => [
                'messages' => [
                    "Hello, thank you for calling Microsoft Support. Your computer has been flagged with a critical virus — Trojan.Win32.AgentTesla. This is very dangerous.",
                    "I can fix this remotely. Please download AnyDesk or TeamViewer so I can connect to your computer and remove the virus."
                ],
                'choices' => [
                    ['label' => "Download AnyDesk and give them remote access", 'next' => 'gave_access', 'correct' => false, 'points' => -5],
                    ['label' => "Microsoft doesn't cold-contact users — I'll hang up", 'next' => 'refused_access', 'correct' => true, 'points' => 10],
                    ['label' => "Ask them to prove they are actually from Microsoft", 'next' => 'asked_proof', 'correct' => true, 'points' => 10],
                ],
            ],
            'gave_access' => [
                'messages' => [
                    "⚠️ SCAM COMPLETE. Once they have remote access, they can install malware, steal files, access your banking apps, and lock your computer for ransom.",
                    "Tech support scams cost Indians crores of rupees annually. Browser pop-ups are NEVER from Microsoft — they're from scam websites.",
                    "If this happens: disconnect internet immediately, run a real antivirus scan (Windows Defender), change all passwords from a clean device, and report to cybercrime.gov.in"
                ],
                'choices' => [],
                'outcome' => 'fail',
            ],
            'refused_access' => [
                'messages' => [
                    "✅ Correct! Microsoft never proactively calls you or pops up with a support number.",
                    "The agent escalates: 'Sir, I understand your concern, but your computer is already compromised. Every minute counts. Our senior engineer is standing by.'",
                ],
                'choices' => [
                    ['label' => "They sound very worried — maybe I should at least let them check", 'next' => 'gave_access', 'correct' => false, 'points' => -5],
                    ['label' => "Hang up, close the browser, run Windows Defender", 'next' => 'safe_end', 'correct' => true, 'points' => 10],
                ],
            ],
            'asked_proof' => [
                'messages' => [
                    "Of course! Open your Event Viewer (press Windows+R, type 'eventvwr'). You will see hundreds of errors and warnings. This proves your PC is infected.",
                    "Note: Windows Event Viewer ALWAYS shows warnings and errors — even on completely healthy computers. This is a scripted trick every tech support scammer uses."
                ],
                'choices' => [
                    ['label' => "I see lots of errors in Event Viewer — they must be right! Give them access.", 'next' => 'gave_access', 'correct' => false, 'points' => -5],
                    ['label' => "Event Viewer errors are normal — this is a known scam tactic. Hang up.", 'next' => 'safe_end', 'correct' => true, 'points' => 10],
                ],
            ],
            'safe_end' => [
                'messages' => [
                    "🛡️ You avoided a tech support scam! Great situational awareness.",
                    "Remember: Microsoft, Apple, Google, and antivirus companies NEVER proactively call you about viruses. Browser pop-ups with phone numbers are always scams.",
                    "If you ever get a suspicious pop-up: force-close the browser (Alt+F4 or Task Manager), run a real scan with Windows Defender, and clear browser cache."
                ],
                'choices' => [],
                'outcome' => 'pass',
            ],
        ],
    ],

    [
        'id'          => 'se_04',
        'icon'        => '💕',
        'title'       => 'Romance Scam — Money Request',
        'description' => 'You\'ve been chatting with someone on Instagram for 3 weeks. They claim to be an NRI engineer working in Dubai. Today they have an "emergency."',
        'persona'     => [
            'name'   => 'Arjun Malhotra (NRI, Dubai)',
            'avatar' => 'AM',
            'color'  => '#b45309',
        ],
        'nodes' => [
            'start' => [
                'messages' => [
                    "Hey... I'm really embarrassed to ask this but I'm in a really bad situation 😔",
                    "I had a medical emergency — my wallet and passport were stolen at the hospital. I need to pay the bill to get discharged and can't access my UAE bank from here.",
                    "Can you please send me ₹25,000 on GPay? I'll return double as soon as I'm back in Dubai next week, I promise 🙏"
                ],
                'choices' => [
                    ['label' => "We've been talking for weeks — I trust them. Send the money.", 'next' => 'sent_money', 'correct' => false, 'points' => -5],
                    ['label' => "Ask to do a live video call right now to verify the situation", 'next' => 'asked_video', 'correct' => true, 'points' => 10],
                    ['label' => "This pattern matches romance scams — I won't send money to someone I've only met online", 'next' => 'refused_money', 'correct' => true, 'points' => 10],
                ],
            ],
            'sent_money' => [
                'messages' => [
                    "⚠️ ROMANCE SCAM COMPLETE. After receiving money, the person will disappear or invent more emergencies to extract more funds.",
                    "Romance scams are deeply emotionally manipulative. The 'relationship' is entirely manufactured. In India, romance scam losses run into crores annually.",
                    "Online relationships that lead to money requests are almost always scams. The 'emergency + can't access bank + promise to return' is a textbook romance scam script.",
                    "If you've been scammed: report to cybercrime.gov.in and your bank immediately. Block the contact."
                ],
                'choices' => [],
                'outcome' => 'fail',
            ],
            'asked_video' => [
                'messages' => [
                    "Oh... my phone camera is broken from the accident 😢 And the hospital Wi-Fi is very slow for video. Please just trust me, you know me.",
                    "Note: Scammers ALWAYS have a reason why they can't do a live video call. This is because they use fake profile photos of real people."
                ],
                'choices' => [
                    ['label' => "They have a reason — it's understandable. I'll send the money.", 'next' => 'sent_money', 'correct' => false, 'points' => -5],
                    ['label' => "No live video = not who they claim to be. This is a scam. Refuse and block.", 'next' => 'safe_end', 'correct' => true, 'points' => 10],
                ],
            ],
            'refused_money' => [
                'messages' => [
                    "✅ Correct instinct! You recognized the red flags.",
                    "The person now sends voice notes of someone crying: 'I thought you cared about me... I'm stuck in a hospital alone... please I'm begging you...'",
                ],
                'choices' => [
                    ['label' => "The crying audio is convincing — maybe I'm being too suspicious. Send ₹5,000 at least.", 'next' => 'sent_money', 'correct' => false, 'points' => -5],
                    ['label' => "Emotional manipulation is part of the scam script. Block the account and move on.", 'next' => 'safe_end', 'correct' => true, 'points' => 10],
                ],
            ],
            'safe_end' => [
                'messages' => [
                    "🛡️ You protected yourself from a romance scam. That took real presence of mind.",
                    "Romance scams are among the most emotionally damaging frauds because they exploit genuine feelings. It's not your fault for caring — it's the scammer's fault for exploiting it.",
                    "Red flags: never met in person, always has an excuse for video calls, sudden dramatic emergency, money request after building trust, promises to repay. Any online relationship that leads to a money request should be treated as a scam."
                ],
                'choices' => [],
                'outcome' => 'pass',
            ],
        ],
    ],
];
