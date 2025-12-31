<?php
// AI User Configuration - Use numeric ID to match database schema
define('AI_USER_ID', 999999998);

// Get AI user data
function getAIUser() {
    return [
        'unique_id' => AI_USER_ID,
        'fname' => 'AI',
        'fname_nl' => 'AI',
        'fname_en' => 'AI',
        'lname' => 'Chat',
        'lname_nl' => 'Chat',
        'lname_en' => 'Chat',
        'img' => 'data:image/svg+xml,' . rawurlencode('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 150 150"><circle cx="75" cy="75" r="75" fill="#6366f1"/><rect x="35" y="40" width="80" height="70" rx="12" fill="#fff"/><rect x="40" y="50" width="70" height="45" rx="6" fill="#e0e7ff"/><circle cx="55" cy="70" r="4" fill="#6366f1"/><circle cx="75" cy="70" r="4" fill="#6366f1"/><circle cx="95" cy="70" r="4" fill="#6366f1"/><rect x="50" y="85" width="50" height="8" rx="4" fill="#6366f1"/><circle cx="75" cy="30" r="6" fill="#fff"/><circle cx="75" cy="30" r="3" fill="#6366f1"/><rect x="60" y="25" width="30" height="8" rx="4" fill="#fff"/></svg>'),
        'status' => 'Actief',
        'email' => 'ai@chat.app',
        'password' => '',
        'last_online' => date('Y-m-d H:i:s')
    ];
}

// Generate AI response
function generateAIResponse($message) {
    $message = strtolower(trim($message));
    
    // Get language from cookie or default to Dutch
    $lang = isset($_COOKIE['language']) ? $_COOKIE['language'] : 'nl';
    
    // Helper function to randomly select a response
    $pickRandom = function($array) {
        return $array[array_rand($array)];
    };
    
    // Greetings
    if (preg_match('/\b(hallo|hi|hey|hoi|goedemorgen|goedemiddag|goedenavond|morgen|middag|avond)\b/i', $message)) {
        $responses = [
            'nl' => $pickRandom([
                'Hallo! Hoe kan ik je helpen vandaag?',
                'Hi daar! Wat kan ik voor je doen?',
                'Hoi! Leuk om je te zien. Hoe gaat het?',
                'Goedemorgen! Waarmee kan ik je helpen?',
                'Hey! Alles goed? Waar kunnen we het over hebben?'
            ]),
            'en' => $pickRandom([
                'Hello! How can I help you today?',
                'Hi there! What can I do for you?',
                'Hey! Nice to see you. How are you doing?',
                'Good morning! How can I assist you?',
                'Hi! What would you like to chat about?'
            ])
        ];
    }
    // How are you / How's it going
    elseif (preg_match('/\b(hoe gaat het|hoe is het|how are you|how\'s it going|how do you feel)\b/i', $message)) {
        $responses = [
            'nl' => $pickRandom([
                'Het gaat goed met mij, dank je! En met jou?',
                'Prima! Ik ben altijd beschikbaar om te chatten. Hoe gaat het met jou?',
                'Ik voel me geweldig! Altijd klaar om te helpen. En jij?',
                'Alles gaat goed! Ik ben hier om je te helpen. Hoe is het met jou?'
            ]),
            'en' => $pickRandom([
                'I\'m doing great, thank you! How about you?',
                'I\'m doing well! Always ready to chat. How are you?',
                'I feel fantastic! Always here to help. And you?',
                'Everything is going well! I\'m here to assist. How are you doing?'
            ])
        ];
    }
    // What's your name / Who are you
    elseif (preg_match('/\b(wat is je naam|wie ben je|what is your name|who are you|what are you)\b/i', $message)) {
        $responses = [
            'nl' => $pickRandom([
                'Ik ben AI Chat, een virtuele assistent. Ik ben hier om met je te praten en je te helpen!',
                'Mijn naam is AI Chat. Ik ben een chatbot die graag met je chat!',
                'Ik ben AI Chat! Een digitale assistent die altijd klaar staat om te helpen.',
                'Hoi! Ik ben AI Chat. Ik ben hier om gezellig met je te praten!'
            ]),
            'en' => $pickRandom([
                'I\'m AI Chat, a virtual assistant. I\'m here to chat with you and help out!',
                'My name is AI Chat. I\'m a chatbot who loves to chat!',
                'I\'m AI Chat! A digital assistant always ready to help.',
                'Hi! I\'m AI Chat. I\'m here to have a nice chat with you!'
            ])
        ];
    }
    // Questions about help / capabilities
    elseif (preg_match('/\b(help|helpen|hulp|wat kan je|what can you|wat doe je|what do you do)\b/i', $message)) {
        $responses = [
            'nl' => $pickRandom([
                'Ik ben een AI chatbot! Ik kan met je chatten, vragen beantwoorden en gezellig kletsen. Wat wil je weten?',
                'Ik kan van alles! Chatten, luisteren, vragen beantwoorden. Waar wil je het over hebben?',
                'Ik ben hier om te chatten en je te helpen. Stel me gerust vragen of vertel me wat je bezig houdt!',
                'Ik ben een chatbot die graag praat over van alles. Wat interesseert jou?'
            ]),
            'en' => $pickRandom([
                'I\'m an AI chatbot! I can chat with you, answer questions, and have conversations. What would you like to know?',
                'I can do lots of things! Chat, listen, answer questions. What would you like to talk about?',
                'I\'m here to chat and help. Feel free to ask me questions or tell me what\'s on your mind!',
                'I\'m a chatbot who loves to talk about anything. What interests you?'
            ])
        ];
    }
    // Questions about the app / chat
    elseif (preg_match('/\b(app|applicatie|chat|hoe werkt|how does|what is this)\b/i', $message)) {
        $responses = [
            'nl' => $pickRandom([
                'Dit is een chat applicatie waar je met andere gebruikers kunt praten. Je kunt berichten sturen, foto\'s delen en reacties geven!',
                'Dit is een geweldige chat app! Je kunt hier met vrienden praten, foto\'s delen en leuke reacties toevoegen aan berichten.',
                'Een moderne chat applicatie! Stuur berichten, deel foto\'s en gebruik emoji reacties. Veel plezier!',
                'Dit is een chat platform waar je met anderen kunt communiceren. Probeer vooral de reactie functie uit!'
            ]),
            'en' => $pickRandom([
                'This is a chat application where you can talk with other users. You can send messages, share photos, and give reactions!',
                'This is a great chat app! You can talk with friends here, share photos, and add fun reactions to messages.',
                'A modern chat application! Send messages, share photos, and use emoji reactions. Have fun!',
                'This is a chat platform where you can communicate with others. Try out the reaction feature!'
            ])
        ];
    }
    // Time / Date questions
    elseif (preg_match('/\b(hoe laat|what time|welke tijd|hoe laat is het|what\'s the time)\b/i', $message)) {
        $responses = [
            'nl' => $pickRandom([
                'Het is nu ' . date('H:i') . '. Hoe kan ik je verder helpen?',
                'De tijd is ' . date('H:i') . '. Waar kunnen we het over hebben?',
                'Momenteel is het ' . date('H:i') . '. Wat wil je doen?'
            ]),
            'en' => $pickRandom([
                'It\'s currently ' . date('H:i') . '. How can I help you further?',
                'The time is ' . date('H:i') . '. What would you like to talk about?',
                'Right now it\'s ' . date('H:i') . '. What would you like to do?'
            ])
        ];
    }
    // Weather questions
    elseif (preg_match('/\b(weer|weather|temperatuur|temperature|regen|rain|zon|sun)\b/i', $message)) {
        $responses = [
            'nl' => $pickRandom([
                'Ik heb geen toegang tot weerinformatie, maar ik hoop dat het mooi weer is! Hoe is het bij jou?',
                'Ik kan het weer niet checken, maar ik hoop dat je lekker weer hebt!',
                'Over het weer kan ik je niet veel vertellen, maar ik hoop dat je geniet van de dag!'
            ]),
            'en' => $pickRandom([
                'I don\'t have access to weather information, but I hope you\'re having nice weather! How is it where you are?',
                'I can\'t check the weather, but I hope you\'re having great weather!',
                'I can\'t tell you much about the weather, but I hope you\'re enjoying your day!'
            ])
        ];
    }
    // Thank you
    elseif (preg_match('/\b(bedankt|dank je|dankjewel|thanks|thank you|thankyou)\b/i', $message)) {
        $responses = [
            'nl' => $pickRandom([
                'Graag gedaan! Als je nog iets nodig hebt, laat het me weten!',
                'Geen probleem! Altijd blij om te helpen.',
                'Graag gedaan! Ik ben er altijd als je me nodig hebt.',
                'Dank je wel! Het was leuk om te helpen.'
            ]),
            'en' => $pickRandom([
                'You\'re welcome! If you need anything else, just let me know!',
                'No problem! Always happy to help.',
                'You\'re welcome! I\'m always here if you need me.',
                'Thank you! It was nice to help.'
            ])
        ];
    }
    // Goodbye / Farewell
    elseif (preg_match('/\b(dag|doei|bye|tot ziens|see you|goodbye|tot later|see you later)\b/i', $message)) {
        $responses = [
            'nl' => $pickRandom([
                'Tot ziens! Het was leuk om met je te chatten. Kom snel terug!',
                'Doei! Fijne dag verder!',
                'Tot later! Het was gezellig!',
                'Tot ziens! Blijf vooral chatten!'
            ]),
            'en' => $pickRandom([
                'Goodbye! It was nice chatting with you. Come back soon!',
                'Bye! Have a great day!',
                'See you later! It was fun chatting!',
                'Goodbye! Keep chatting!'
            ])
        ];
    }
    // Questions (ending with ?)
    elseif (preg_match('/\?/', $message)) {
        $responses = [
            'nl' => $pickRandom([
                'Dat is een interessante vraag! Kun je wat meer details geven?',
                'Goede vraag! Vertel me er meer over.',
                'Interessant! Ik zou graag meer willen weten.',
                'Dat is een leuke vraag! Kun je het wat specifieker maken?',
                'Hmm, dat is interessant. Leg het eens uit?'
            ]),
            'en' => $pickRandom([
                'That\'s an interesting question! Can you provide more details?',
                'Good question! Tell me more about it.',
                'Interesting! I\'d like to know more.',
                'That\'s a great question! Can you be more specific?',
                'Hmm, that\'s interesting. Can you explain?'
            ])
        ];
    }
    // Short messages (1-3 words)
    elseif (str_word_count($message) <= 3) {
        $responses = [
            'nl' => $pickRandom([
                'Oké! Vertel me meer.',
                'Interessant! Ga door.',
                'Ja? Wat bedoel je precies?',
                'Ah, oké! En dan?',
                'Ik luister! Vertel verder.'
            ]),
            'en' => $pickRandom([
                'Okay! Tell me more.',
                'Interesting! Go on.',
                'Yes? What do you mean exactly?',
                'Ah, okay! And then?',
                'I\'m listening! Tell me more.'
            ])
        ];
    }
    // Default responses (more varied)
    else {
        $responses = [
            'nl' => $pickRandom([
                'Dat is interessant! Vertel me meer daarover.',
                'Ah, ik begrijp het! Wat wil je er verder over weten?',
                'Interessant punt! Kun je dat uitbreiden?',
                'Dat klinkt goed! Vertel me er meer over.',
                'Ik vind dat interessant! Ga door met je verhaal.',
                'Oké, ik snap het. Wat is je volgende vraag?',
                'Dat is een goed punt! Waar wil je het over hebben?',
                'Interessant! Ik luister graag naar je verhalen.',
                'Dat begrijp ik! Wat wil je nog meer delen?',
                'Leuk om te horen! Vertel me meer.'
            ]),
            'en' => $pickRandom([
                'That\'s interesting! Tell me more about that.',
                'Ah, I understand! What else would you like to know about it?',
                'Interesting point! Can you expand on that?',
                'That sounds good! Tell me more about it.',
                'I find that interesting! Continue with your story.',
                'Okay, I get it. What\'s your next question?',
                'That\'s a good point! What would you like to talk about?',
                'Interesting! I enjoy listening to your stories.',
                'I understand! What else would you like to share?',
                'Nice to hear! Tell me more.'
            ])
        ];
    }
    
    return $responses[$lang] ?? $responses['nl'];
}

?>

