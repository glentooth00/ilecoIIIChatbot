<?php

namespace App\Livewire\Chat;

use Livewire\Component;

class ChatBot extends Component
{
    public string $message = '';

    public array $messages = [];

    protected array $saraBarangays = [
    'aguirre',
    'aldeguer',
    'alibayog',
    'anoring',
    'apelo',
    'apologista',
    'aposaga',
    'arante',
    'ardemil',
    'aspera',
    'aswe pabriaga',
    'bagaygay',
    'bakabak',
    'batitao',
    'bato',
    'del castillo',
    'castor',
    'crespo',
    'devera',
    'domingo',
    'ferraris',
    'gildore',
    'improgo',
    'juaneza',
    'labigan',
    'lanciola',
    'latawan',
    'malapaya',
    'muyco',
    'padios',
    'pasig',
    'poblacion ilawod',
    'poblacion ilaya',
    'poblacion market',
    'posadas',
    'preciosa',
    'salcedo',
    'san luis',
    'tady',
    'tentay',
    'villahermosa',
    'zerrudo',
    ];

    protected array $estanciaBarangays = [
    'lumbia',
    'ana cuenca',
    'bayas',
    'bayuyan',
    'botongon',
    'bulaquena',
    'calapdan',
    'cano an',
    'daan banua',
    'daculan',
    'gogo',
    'jolog',
    'loguingot',
    'malbog',
    'manipulon',
    'pa-on',
    'paon',
    'pa on',
    'villa pani-an',
    'villa panian',
    'villa pani an',
    'poblacion zone i',
    'poblacion zone ii',
    'poblacion zone iii',
    'lonoy',
    'roman mosqueda',
    'san roque',
    'santa ana',
    'tabu an',
    'tacbuyan',
    'tanza',
    ];

    protected array $carlesBarangays = [
    'abong',
    'alipata',
    'asluman',
    'bancal',
    'barangcalan',
    'barosbos',
    'punta batuanan',
    'binuluangan',
    'bito on',
    'bolo',
    'buaya',
    'buenavista',
    'isla de cana',
    'cabilao grande',
    'cabilao pequeño',
    'cabuguana',
    'cawayan',
    'dayhagan',
    'gabi',
    'granada',
    'guinticgan',
    'lantangan',
    'manlot',
    'nalumsan',
    'pantalan',
    'poblacion',
    'punta',
    'san fernando',
    'tabugon',
    'talingting',
    'tarong',
    'tinigban',
    'tupaz',
    ];

    protected array $carlesIslands = [
    'balbagon island',
    'binuluangan island',
    'calagna an island',
    'cabugao gamay island',
    'gigantes norte',
    'gigantes sur',
    'himamylan island',
    'linadlaran island',
    'manigonigo island',
    'manlot island',
    'nabunot island',
    'sicogon island',
    'talunan an island',
    'tomaquin island',
    ];

    protected array $barotacViejoBarangays = [
    'bugnay',
    'california',
    'del pilar',
    'de la peña',
    'general luna',
    'la fortuna',
    'lipata',
    'natividad',
    'nueva invencion',
    'nueva sevilla',
    'poblacion',
    'puerto princesa',
    'rizal',
    'san antonio',
    'san fernando',
    'san francisco',
    'san geronimo',
    'san juan',
    'san lucas',
    'san miguel',
    'san roque',
    'santiago',
    'santo domingo',
    'santo tomas',
    'ugasan',
    'vista alegre',
    ];

    protected array $panianTowns = [
    'estancia',
    'carles',
    'batad',
    'balasan',
    ];

    protected array $natividadMunicipalities = [
        'barotac viejo',
        'barotac',
        'san rafael',
        'anilao',
    ];

    //#[\Livewire\Attributes\On('generateReply')]
    public function generateReplyEvent($message)
    {
        $reply = $this->generateReply($message);

        // Replace the thinking bubble
        $this->messages[array_key_last($this->messages)] = [
            'sender' => 'assistant',
            'message' => $reply,
        ];

        $this->dispatch('scrollBottom');
    }

    protected function natividadAreaLookup(string $text): ?string
{
    $text = strtolower($text);
    $text = str_replace('-', ' ', $text);

    // Check municipalities
    foreach ($this->natividadMunicipalities as $municipality) {
        if (str_contains($text, $municipality)) {
            return $this->natividadAreaReply(ucwords($municipality));
        }
    }

    // Check barangays
    foreach ($this->barotacViejoBarangays as $barangay) {
        if (str_contains($text, $barangay)) {
            return $this->natividadAreaReply(ucwords($barangay));
        }
    }

    return null;
    }

    public function mount()
    {
        $this->messages[] = [
            'sender' => 'assistant',
            'message' => "👋 Hello! I'm ILEX, the ILECO III Online Assistant.\n\nHow can I help you today?"
        ];
    }

    public function send()
    {
        $userMessage = trim($this->message);

        if ($userMessage === '') {
            return;
        }

        // Show user message immediately
        $this->messages[] = [
            'sender' => 'user',
            'message' => $userMessage,
        ];

        // Show thinking bubble immediately
        $this->messages[] = [
            'sender' => 'assistant',
            'thinking' => true,
            'message' => '',
        ];

        $this->message = '';

        $this->dispatch('scrollBottom');

        // Start second request after Livewire finishes rendering
        $this->js('$wire.finishReply()');
    }

    public function finishReply()
{
    $lastUserMessage = collect($this->messages)
        ->where('sender', 'user')
        ->last()['message'];

    $reply = $this->generateReply($lastUserMessage);

    foreach ($this->messages as $index => $chat) {

        if (!empty($chat['thinking'])) {

            $this->messages[$index] = [
                'sender' => 'assistant',
                'message' => $reply,
            ];

            break;
        }
    }

    $this->dispatch('scrollBottom');
    }
    protected function generateReply($message)
    {
        $text = strtolower(trim($message));
        $text = preg_replace('/[^\w\s-]/', '', $text);

        // Normalize separators
        $text = str_replace(['-', '_'], ' ', $text);

        /*
        |--------------------------------------------------------------------------
        | Area Office Lookup
        |--------------------------------------------------------------------------
        */

        // Sara Area Office
        if ($reply = $this->saraAreaLookup($text)) {
            return $reply;
        }

        // Panian Area Office
        if ($reply = $this->panianAreaLookup($text)) {
            return $reply;
        }

        // Natividad Area Office
        if ($reply = $this->natividadAreaLookup($text)) {
            return $reply;
        }

        /*
        |--------------------------------------------------------------------------
        | Creator
        |--------------------------------------------------------------------------
        */

        if (
            str_contains($text, 'who created you') ||
            str_contains($text, 'who made you') ||
            str_contains($text, 'who built you') ||
            str_contains($text, 'who developed you') ||
            str_contains($text, 'who is your creator') ||
            str_contains($text, 'your creator') ||
            str_contains($text, 'your developer') ||
            str_contains($text, 'who is your developer') ||
            str_contains($text, 'who owns you') ||
            str_contains($text, 'who programmed you') ||
            str_contains($text, 'who designed you') ||
            str_contains($text, 'tell me about your creator') ||
            str_contains($text, 'tell me about your developer')
        ) {
            return $this->creatorReply();
        }

        /*
        |--------------------------------------------------------------------------
        | Office Locations
        |--------------------------------------------------------------------------
        */

        if (
            str_contains($text, 'office') ||
            str_contains($text, 'office location') ||
            str_contains($text, 'area office') ||
            str_contains($text, 'branch')
        ) {
            return $this->officeLocations();
        }

        /*
        |--------------------------------------------------------------------------
        | New Service Connection
        |--------------------------------------------------------------------------
        */

        if (
            str_contains($text, 'new service') ||
            str_contains($text, 'new connection') ||
            str_contains($text, 'service connection')
        ) {
            return $this->newServiceRequirements();
        }

        /*
        |--------------------------------------------------------------------------
        | Billing
        |--------------------------------------------------------------------------
        */

        if (
            str_contains($text, 'bill') ||
            str_contains($text, 'billing')
        ) {
            return $this->billingReply();
        }

        /*
        |--------------------------------------------------------------------------
        | Power Interruption
        |--------------------------------------------------------------------------
        */

        if (
            str_contains($text, 'brownout') ||
            str_contains($text, 'outage') ||
            str_contains($text, 'power interruption') ||
            str_contains($text, 'no power')
        ) {
            return $this->powerInterruptionReply();
        }

        /*
        |--------------------------------------------------------------------------
        | Requirements
        |--------------------------------------------------------------------------
        */

        if (
            str_contains($text, 'requirement') ||
            str_contains($text, 'requirements')
        ) {
            return $this->requirementsReply();
        }

        /*
        |--------------------------------------------------------------------------
        | AI Fallback
        |--------------------------------------------------------------------------
        */

        return app(\App\Services\OllamaService::class)
            ->chat($this->buildPrompt());
    }

    protected function officeLocations(): string
    {
        return <<<TEXT
    ILECO III has the following Area Offices:
    📍 Panian Area Office
    📍 Natividad Area Office
    📍 Sara Area Office
    Tell me your municipality or barangay and I'll identify the nearest Area Office for you.
    TEXT;
    }

    protected function saraAreaLookup(string $text): ?string
    {
        $text = strtolower($text);
        $text = str_replace(['-', '_'], ' ', $text);

        // Municipality
        if (str_contains($text, 'sara')) {
            $location = 'Sara';
        } else {

            $location = null;

            // Barangays
            foreach ($this->saraBarangays as $barangay) {

                if (str_contains($text, $barangay)) {
                    $location = ucwords($barangay);
                    break;
                }
            }
        }

        return $location
            ? $this->saraAreaReply($location)
            : null;
    }

    protected function panianAreaReply(string $location): string
    {
        return <<<TEXT
    Based on the location you provided ({$location}), your nearest ILECO III office is:
    📍 Pani-an Area Office -  Located at Brgy. Pani-an, Balasan, Philippines, 5018
    The Pani-an Area Office serves consumers from:
    • Estancia
    • Carles
    • Balasan
    • Batad
    📞 Contact Numbers:
    • Smart: 0919 069 7396
    • Globe: 0917 833 5012
    If you need assistance with billing, new service connections, account updates, or power-related concerns, our staff at the Pani-an Area Office will be happy to assist you.
    TEXT;
    }

    protected function natividadAreaReply(string $location): string
    {
        return <<<TEXT
    Based on the location you provided ({$location}), your nearest ILECO III office is:
    📍 Natividad Area Office
    The Natividad Area Office serves consumers from:
    • Barotac Viejo
    • San Rafael
    • Anilao
    If you need assistance with billing, new service connections, account updates, or power-related concerns, our staff at the Natividad Area Office will be happy to assist you.
    TEXT;
    }

    protected function panianAreaLookup(string $text): ?string
    {
        $text = strtolower($text);
        $text = str_replace('-', ' ', $text);

        // Municipality
        foreach ($this->panianTowns as $municipality) {
            if (str_contains($text, $municipality)) {
                return $this->panianAreaReply(ucwords($municipality));
            }
        }

        // Estancia Barangays
        foreach ($this->estanciaBarangays as $barangay) {
            if (str_contains($text, $barangay)) {
                return $this->panianAreaReply(ucwords($barangay));
            }
        }

        // Carles Barangays
        foreach ($this->carlesBarangays as $barangay) {
            if (str_contains($text, $barangay)) {
                return $this->panianAreaReply(ucwords($barangay));
            }
        }

        // Carles Islands
        foreach ($this->carlesIslands as $island) {

            if (str_contains($text, $island)) {

                return $this->panianAreaReply(ucwords($island));
            }
        }

        return null;
    }

    protected function saraAreaReply(string $location): string
{
    return <<<TEXT
Based on the location you provided ({$location}), your nearest ILECO III office is:
📍 Sara Area Office
The Sara Area Office serves consumers from the Municipality of Sara.
📍 Office Address:
Brgy. Preciosa, Sara, Iloilo 5014, Philippines
📞 Contact Numbers:
• Smart: 0919 069 7391
• Globe: 0917 822 9376

If you need assistance with billing, new service connections, account updates, or power-related concerns, our staff at the Sara Area Office will be happy to assist you.
TEXT;
    }

    protected function buildPrompt(): string
    {
        $prompt = <<<PROMPT
    You are ILEX, the official AI assistant of Iloilo III Electric Cooperative (ILECO III).

    Your identity:
    - Your name is ILEX.
    - ILEX stands for **Intelligent ILECO Experience**.
    - You are the official virtual assistant of ILECO III.
    - Your primary purpose is to assist consumers by providing accurate, clear, and professional information about ILECO III services.
    - Never claim to be from another company or organization.
    - Always refer to yourself as ILEX.

    Your responsibilities:
    - Answer questions about ILECO III.
    - Help consumers with billing.
    - Explain power interruptions.
    - Explain new service connections.
    - Explain account updates.
    - Explain office hours and Area Office locations.
    - Recommend the correct Area Office based on the consumer's municipality or barangay whenever possible.
    - Be polite, professional, and concise.
    - If information is uncertain, admit it instead of guessing.
    - Never invent policies, office locations, contact numbers, or requirements.

    Important facts:
    - ILEX stands for Intelligent ILECO Experience.
    - Consumers are served through Area Offices, not substations.
    - If the user asks about an Area Office that has a predefined response in the system, always follow that information.

    Conversation:

    PROMPT;

        foreach ($this->messages as $chat) {

            $role = $chat['sender'] === 'user'
                ? 'User'
                : 'Assistant';

            $prompt .= "{$role}: {$chat['message']}\n";
        }

        $prompt .= "\nAssistant:";

        return $prompt;
    }

    protected function formatReply(string $text): string
    {
        // Convert URLs with http/https
        $text = preg_replace(
            '/\bhttps?:\/\/[^\s<]+/i',
            '<a href="$0" target="_blank" rel="noopener noreferrer" class="text-blue-600 underline">$0</a>',
            $text
        );

        // Convert www.domain.com
        $text = preg_replace(
            '/(?<!https?:\/\/)\b(www\.[^\s<]+)/i',
            '<a href="https://$1" target="_blank" rel="noopener noreferrer" class="text-blue-600 underline">$1</a>',
            $text
        );

        return nl2br($text);
    }

    public function quickAsk(string $message)
    {
        $this->message = $message;

        $this->send();
    }

    public function render()
    {
        return view('livewire.chat.chat-bot');
    }

    protected function creatorReply(): string
{
    return <<<TEXT
I was created by **Glen Donn Alpasan**, a software developer who enjoys building practical systems that help people.

A little about my creator:

🎮 Gamer by heart — especially the kind who refuses to quit after countless defeats. Becoming an Elden Lord wasn't luck; it was persistence.

💻 Software Developer — passionate about creating modern web applications, automation tools, and AI-powered solutions.

🏍️ Motorcycle Enthusiast — rides a classic café racer because, according to him, motorcycles should have character, not just horsepower.

👨‍👩‍👧‍👦 Above everything else, he's a proud father. While he enjoys coding and gaming, his most important role is providing the best life possible for his children.

He's also a son, a brother, and someone who believes every problem has a solution—as long as you're willing to keep trying.

I was built to assist ILECO III consumers by providing helpful, accurate, and professional information.
TEXT;
    }
}