<div class="mx-auto mt-5 w-full max-w-xl">

    

    <!-- Prompt Box -->
    <div class="overflow-hidden rounded-3xl border border-zinc-200 bg-white shadow-xl">

        <!-- Input -->
        <div
            x-data="{ message: @entangle('message').live }"
            class="relative p-6">

            <!-- Placeholder -->
        @if(count($messages))

<div
    id="chat-container"
    x-data
    x-ref="messages"
    x-on:scroll-bottom.window="
        $nextTick(() => {
            $refs.messages.scrollTop = $refs.messages.scrollHeight;
        })
    "
    class="h-[320px] overflow-y-auto space-y-4 scroll-smooth"
    style="height:25em;">

@foreach($messages as $chat)

    @if($chat['sender'] === 'user')

        <div class="flex justify-end">
            <div class="max-w-md rounded-2xl bg-blue-600 px-4 py-3 text-white">
                {{ $chat['message'] }}
            </div>
        </div>

  @else

    @if(!empty($chat['thinking']))

        <div class="flex items-start gap-3">

            <div class="mt-1 text-2xl">
                💬
            </div>

            <div class="flex items-center gap-2 text-zinc-500">

                <div class="h-2 w-2 rounded-full bg-blue-500 animate-bounce"></div>

                <div class="h-2 w-2 rounded-full bg-blue-500 animate-bounce [animation-delay:150ms]"></div>

                <div class="h-2 w-2 rounded-full bg-blue-500 animate-bounce [animation-delay:300ms]"></div>

                <span class="ml-2 text-sm">
                    ILEX is thinking...
                </span>

            </div>

        </div>

    @else

        <div class="flex items-start gap-3">

            <div class="mt-1 text-2xl">
                💬
            </div>

            <div class="whitespace-pre-line text-zinc-700">
                {!! $this->formatReply($chat['message']) !!}
            </div>

        </div>

    @endif

@endif

@endforeach


</div>

        @else

        <!-- Default Placeholder -->
<div
    x-show="message.length === 0 && {{ count($messages) === 0 ? 'true' : 'false' }}"
    x-transition
    class="absolute inset-x-6 top-6 z-10 pointer-events-none">

    <div class="flex items-start gap-2">

        <div class="text-2xl">
            💬
        </div>

        <div class="min-w-0">
            <h2 class="font-semibold text-xl text-zinc-800">
                How can I help you today?
            </h2>

            <p class="text-base leading-7 text-zinc-500">
                Ask about billing, power interruptions, new service connections,
                account updates, payment options, or any ILECO III service.
            </p>

        </div>

    </div>

</div>

    @endif

            {{-- <textarea
                wire:model.live="message"
                x-model="message"
                rows="5"
                x-on:input="
                    $el.style.height='auto';
                    $el.style.height=Math.min($el.scrollHeight,300)+'px';
                "
                placeholder=""
                class="relative w-full resize-none overflow-y-auto border-0 bg-transparent p-0 text-base leading-7 focus:ring-0"
                style="max-height:300px"></textarea> --}}

        </div>

        <!-- Suggestions -->
        <div class="border-t border-zinc-100 px-5 pt-4">

            <div class="flex flex-wrap gap-2 pb-4">

                <button
                    wire:click="quickAsk('Report Power Interruption')"
                    type="button"
                    class="cursor-pointer rounded-full border border-zinc-200 bg-zinc-50 px-4 py-2 text-sm transition hover:border-blue-500 hover:bg-blue-50">
                    ⚡ Report Power Interruption
                </button>

                <button
                    wire:click="quickAsk('Check My Bill')"
                    type="button"
                    class="cursor-pointer rounded-full border border-zinc-200 bg-zinc-50 px-4 py-2 text-sm transition hover:border-blue-500 hover:bg-blue-50">
                    💳 Check My Bill
                </button>

                <button
                    wire:click="quickAsk('New Service Connection')"
                    type="button"
                    class="cursor-pointer rounded-full border border-zinc-200 bg-zinc-50 px-4 py-2 text-sm transition hover:border-blue-500 hover:bg-blue-50">
                    📄 New Service Connection
                </button>

                <button
                    wire:click="quickAsk('Change Account Name')"
                    type="button"
                    class="cursor-pointer rounded-full border border-zinc-200 bg-zinc-50 px-4 py-2 text-sm transition hover:border-blue-500 hover:bg-blue-50">
                    🏠 Change Account Name
                </button>

                <button
                    wire:click="quickAsk('Office Location')"
                    type="button"
                    class="cursor-pointer rounded-full border border-zinc-200 bg-zinc-50 px-4 py-2 text-sm transition hover:border-blue-500 hover:bg-blue-50">
                    📍 Office Location
                </button>

                <button
                    wire:click="quickAsk('Requirements')"
                    type="button"
                    class="cursor-pointer rounded-full border border-zinc-200 bg-zinc-50 px-4 py-2 text-sm transition hover:border-blue-500 hover:bg-blue-50">
                    📋 Requirements
                </button>

            </div>

        </div>

        <!-- Footer -->
<form
    x-data
    x-ref="form"
    x-on:scroll-bottom.window="
        $nextTick(() => {
            $refs.form.querySelector('input').focus();
        })
    "
    wire:submit="send"
    class="flex items-center gap-3 border-t border-zinc-100 px-5 py-4">

<input
    x-ref="chatInput"
    type="text"
    wire:model.live="message"
    placeholder="Ask ILEX anything..."
    class="flex-1 rounded-xl border border-zinc-200 bg-zinc-50 px-4 py-3 text-sm
           placeholder:text-zinc-400
           focus:border-blue-500
           focus:bg-white
           focus:outline-none
           focus:ring-2
           focus:ring-blue-100"
           autofocus>

    <button
        type="submit"
        class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-600 text-white transition hover:bg-blue-700">

        <svg xmlns="http://www.w3.org/2000/svg"
             class="h-5 w-5"
             fill="none"
             viewBox="0 0 24 24"
             stroke="currentColor">

            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M5 12h14M13 5l7 7-7 7"/>

        </svg>

    </button>

</form>

        </div>

</div>

@script
<script>
Livewire.on('scrollBottom', () => {

    setTimeout(() => {

        const chat = document.getElementById('chat-container');

        if (chat) {
            chat.scrollTo({
                top: chat.scrollHeight,
                behavior: 'smooth'
            });
        }

    }, 50);

});
</script>
@endscript