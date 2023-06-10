<x-app-layout>
    @push('title', __('Shop'))

    <div class="col-span-12">
        <div class="inline-block p-3 bg-white border rounded shadow dark:bg-gray-800 dark:border-gray-900">
            <p class="dark:text-white">{{ __('Your current balance: :balance', ['balance' => auth()->user()->website_balance]) }}</p>
        </div>
    </div>

    <div class="col-span-12 md:col-span-7 lg:col-span-8 xl:col-span-9">
        <div class="flex flex-col gap-y-2 dark:text-gray-300">
            @foreach ($articles as $article)
                <x-shop.packages :article="$article" />

                <style>
                    .{{ $article->icon }} {
                        background: {{ $article->color }};
                    }
                </style>
            @endforeach
        </div>
    </div>

    <div class="row-start-2 md:row-auto col-span-12 flex flex-col gap-y-3 md:col-span-5 lg:col-span-4 xl:col-span-3">
        <x-content.content-card icon="hotel-icon" classes="border dark:border-gray-900">
            <x-slot:title>
                {{ __('Top up account') }}
            </x-slot:title>

            <x-slot:under-title>
                {{ __('Donate to :hotel', ['hotel' => setting('hotel_name')]) }}
            </x-slot:under-title>

            <form action="{{ route('paypal.process-transaction') }}" method="GET">
                @csrf

                <x-form.input name="amount" type="number" value="0" placeholder="amount" />

                <button type="submit" class="mt-2 w-full rounded bg-blue-600 hover:bg-blue-700 text-white p-2 border-2 border-blue-500 transition ease-in-out duration-150 font-semibold">
                {{ __('Donate') }}
                </button>
            </form>
        </x-content.content-card>
    
        <x-content.content-card icon="hotel-icon" classes="border dark:border-gray-900">
            <x-slot:title>
                {{ __(':hotel Shop', ['hotel' => setting('hotel_name')]) }}
            </x-slot:title>

            <x-slot:under-title>
                {{ __('Purchase :hotel items', ['hotel' => setting('hotel_name')]) }}
            </x-slot:under-title>

            <div class="space-y-4 text-[14px] dark:text-gray-300">
                <p>
                    {{ __('Here at :hotel Hotel we are accepting donations to keep the hotel up & running and as a thank you, you will in return receive in-game goods.', ['hotel' => setting('hotel_name')]) }}
                </p>

                <p>
                    <span class="font-semibold">{{ __('Why are donations important?') }}</span><br>
                    {{ __('Donations are important, as it will help to pay our monthly bills needed to keep the hotel up & running, as well as adding new and exciting features for you and others to enjoy!') }}
                </p>

                <p class="font-semibold italic">
                    {{ __('To purchase items from the :hotel shop, please visit our Discord and contact the owner of :hotel Hotel to make your purchase', ['hotel' => setting('hotel_name')]) }}
                </p>

                <div class="mt-4">
                    <a href="{{ setting('discord_invitation_link') }}" target="_blank">
                        <x-form.secondary-button>
                            {{ __('Take me to the :hotel Discord', ['hotel' => setting('hotel_name')]) }}
                        </x-form.secondary-button>
                    </a>
                </div>
            </div>
        </x-content.content-card>
    </div>

    @push('javascript')
        <script type="module">
            tippy('.user-badge');
        </script>
    @endpush
</x-app-layout>
