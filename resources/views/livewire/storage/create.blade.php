@can('create', App\Models\S3Storage::class)
    <div class="w-full">
        <div class="mb-4">For more details, please visit the <a class="underline dark:text-warning"
                href="https://coolify.io/docs/knowledge-base/s3/introduction" target="_blank">Coolify Docs</a>.</div>
        <form class="flex flex-col gap-2" wire:submit='submit'>
            <div class="flex gap-2">
                <x-forms.input required label="Name" id="name" />
                <x-forms.input label="Description" id="description" />
            </div>
            <x-forms.domain-input id="endpointParts" errorId="endpoint" host-label="Host"
                host-placeholder="minio.internal or 192.168.1.50" />
            <div class="flex gap-2">
                <x-forms.input required label="Bucket" id="bucket" />
                <x-forms.input required helper="Region only required for AWS. Leave it as-is for other providers."
                    label="Region" id="region" />
            </div>
            <div class="flex gap-2">
                <x-forms.input required type="password" label="Access Key" id="key" />
                <x-forms.input required type="password" label="Secret Key" id="secret" />
            </div>
            <x-forms.listbox required id="signatureVersion" label="Signature version"
                helper="Signature used when uploading backups. Keep S3v4 unless your provider only supports S3v2." :options="[
                ['value' => 'S3v4', 'label' => 'S3v4'],
                ['value' => 'S3v2', 'label' => 'S3v2 (legacy)'],
            ]" />

            <x-forms.button class="mt-4" type="submit" wire:target="submit">
                Validate Connection & Continue
            </x-forms.button>
        </form>
    </div>
@else
    <x-callout type="danger" title="Insufficient Permissions">
        You don't have permission to create new S3 storage configurations. Please contact your team administrator for
        access.
    </x-callout>
@endcan
