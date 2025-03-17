@section('content')
    <x-form :method="isset($blog) ? 'PUT' : 'POST'" :action="isset($blog) ? route('blogs.update', ['id' => $blog->id]) : route('blogs.store')">

        <x-input name="name" :errors="$errors" :value="isset($blog) ? $blog->name : ''" />
        <x-input name="short_description" :errors="$errors" :value="isset($blog) ? $blog->short_description : ''" />
        <x-input type="textarea" name="description" :errors="$errors" :value="isset($blog) ? $blog->description : ''" />
        <x-input type="select" name="blog_status" :errors="$errors" :value="isset($blog) ? $blog->blog_status->value : ''" :label="__('validation.attributes.status')" :options="App\Enums\BlogStatusEnum::toArray()" />
        <x-button-submit name="submit" :errors="$errors">
            {{ isset($blog) ? __('Edit') : __('Create') }}
        </x-button-submit>

    </x-form>
@endsection
