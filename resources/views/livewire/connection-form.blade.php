<div>
    <x-form-section submit="submit">
        <x-slot name="title">
            Connection data
        </x-slot>

        <x-slot name="description">
            Server credentials.
        </x-slot>

        <x-slot name="form">
            @if(Auth::user()->role == 'admin')
            <div class="col-span-4">
                <x-jet-label for="user_id" value="{{ __('User_Name') }}" />
                    <x-select name="user_id" class="mt-1" wire:model.defer="connection.user_id">
                        <option value=""></option>
                        @foreach($this->users as $user)
                            <option value="{{ $user->id }}">
                                {{ $user->first_name }}
                            </option>
                        @endforeach
                    </x-select>
                <x-jet-input-error for="connection.user_id" class="mt-2" />
            </div>
            @endif
            <div class="col-span-6">
                <x-jet-label for="name" value="{{ __('Name') }}" />
                <x-jet-input id="name" type="text" class="mt-1 block w-full" wire:model.defer="connection.name" />
                <x-jet-input-error for="connection.name" class="mt-2" />
                @if($this->is_user_alredy_exist == 'yes')
                <span class="mt-2" style="color:red;">User Already have a connection</span>
                @endif
            </div>
            <div class="col-span-6 sm:col-span-3">
                <x-jet-label for="base_url" value="{{ __('Url') }}" />
                <x-jet-input id="base_url" type="text" class="mt-1 block w-full" wire:model.defer="connection.base_url" />
                <x-jet-input-error for="connection.base_url" class="mt-2" />
                @if($this->url_redirection == 'true')
                <span class="mt-2" style="color:red;">Connection url is not valid</span>
                @endif
            </div>
            <x-jet-input id="user_id" type="hidden" class="mt-1 block w-full" wire:model.defer="{{Auth::user()->id}}" />
        </x-slot>
        
        <x-slot name="actions">
            <x-jet-button>
                {{ __(!$this->connection->exists ? 'Create' : 'Update') }}
            </x-jet-button>
        </x-slot>
    </x-form-section>
</div>
