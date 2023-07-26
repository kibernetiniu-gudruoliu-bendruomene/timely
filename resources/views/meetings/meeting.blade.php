<x-app-layout>
    <div class="flex flex-col sm:justify-center items-center py-8 sm:pt-0 bg-gray-100 text-black">
        <div
            class="w-full sm:max-w-md md:max-w-lg lg:max-w-xl xl:max-w-5xl mt-6 px-6 py-4 bg-white shadow-xl overflow-hidden rounded-lg">
            @if (Auth::check())
                @include ('meetings.edit-times')
            @endif
            <h2 class="text-xl text-gray-800 leading-tight">
                Meeting: {{ $meeting->title }}
            </h2>
            Description: {{ $meeting->description }}<br>
            Location: {{ $meeting->location }}<br>
            Timezone: {{ $meeting->timezone }}<br>
            Duration (min): {{ $meeting->duration }}<br>
            Meeting link: <a href="">https://domain.com/{{ $meeting->id }}</a><br>
            Created at: {{ $meeting->created_at }}<br>
            Updated at: {{ $meeting->updated_at }}<br>

            Dates and times for the meeting:
            <div class="text-right pr-16">
                <button class="btn" onclick="my_modal_1.showModal()">Vote on times</button>
            </div>
            <dialog id="my_modal_1" class="modal">
                <form method="POST" action="{{ route('vote.store') }}" class="modal-box bg-white shadow-2xl">
                    @csrf
                    <input type="hidden" name="meeting_id" value="{{ $meeting->id }}">

                    <h3 class="font-bold text-lg text-black">Check every time you want to vote on and enter your name to
                        save your votes</h3>
                    <p class="py-4 text-black">Note: Name will be visible to everyone viewing this meeting</p>

                    <div>
                        <x-input-label for="voted_by" :value="__('Your name')"/>
                        <x-text-input id="voted_by" class="block mt-1 w-full text-black" type="text" name="voted_by"
                                      :value="old('voted_by')" required autofocus/>
                        <x-input-error :messages="$errors->get('voted_by')" class="mt-2 text-red-700"/>
                    </div>
                    @foreach($dates as $date)
                        <input type="hidden" name="date_ids[]" value="{{ $date->id }}">
                        <div class="form-control">
                            <label class="label cursor-pointer">
                                <span class="label-text text-lg text-black">Vote on: {{$date->date_and_time}}</span>
                                <div class="shadow-xl">
                                    <input type="checkbox" name="votes[]" value="{{ $date->id }}"
                                           class="checkbox checkbox-lg checkbox-success" checked="checked">
                                </div>
                            </label>
                        </div>
                    @endforeach
                    <div class="flex items-center justify-end mt-4">
                        <x-primary-button type="submit" class="ml-4">
                            {{ __('Save votes') }}
                        </x-primary-button>
                    </div>
                </form>
            </dialog>

            <ul class="m-6">
                @foreach($dates as $date)
                    <li class="my-6 shadow-xl p-6 rounded-xl">
                        <input type='hidden' name='meeting_id' value='{{$meeting->id}}'>
                        <div class="flex justify-center">
                            <div class="mx-6">
                                {{$date->date_and_time}}
                            </div>
                            <div class="mr-2 font-bold">
                                Votes:
                            </div>
                            <div class="tooltip" data-tip="Names of people who voted">
                                Number of votes
                            </div>
                        </div>
                        <div class="container">
                            <div class="inline-flex rounded-md shadow-sm">
                                <!--Update chosen date-->
                                @if (Auth::check())
                                    @if ($user->id == $meeting->user_id)
                                        <form method="POST" action="{{ route('dates.update', ['id' => $date->id]) }}">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="meeting_id" value="{{ $meeting->id }}">
                                            <x-input-label for="new_time" :value="__('Select new time:')"/>
                                            <div class="inline-flex">
                                                <x-flatpickr name='new_time' show-time :min-date="now()->addMinutes(30)"
                                                             :max-date="today()->addDays(90)" required/>
                                            </div>
                                            <x-primary-button type="submit" class="link-button mt-2">
                                                Confirm
                                            </x-primary-button>
                                            @error('new_time')
                                            <p class="text-red-500 text-sm">{{ "The selected date already exists." }}</p>
                                            @enderror
                                            <!--Delete chosen date-->
                                        </form>
                                        <form method="POST" action="{{ route('dates.destroy', ['id' => $date->id]) }}"
                                              onsubmit="return confirm('Are you sure you wish to delete this date?');">
                                            @csrf
                                            @method('DELETE')
                                            <x-primary-button type="submit" class="mt-7 ml-1">
                                                Delete
                                            </x-primary-button>
                                        </form>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
            @if (Auth::check())
                @if ($meeting->user_id == Auth::User()->id)
                @endif
                <a href='/meeting/{{ $meeting->id }}/edit'>
                    <button class='btn btn-warning'>Edit meeting</button>
                </a>
                <a href='/meeting/{{ $meeting->id }}/delete'>
                    <button class='btn btn-error'>Delete meeting</button>
                </a>
            @endif
        </div>
    </div>
</x-app-layout>
