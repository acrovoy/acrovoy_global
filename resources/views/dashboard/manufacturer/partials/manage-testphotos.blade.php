<form 
    action="{{ route('media.upload') }}" 
    method="POST" 
    enctype="multipart/form-data"
>
    @csrf

    <input type="hidden" name="collection" value="test_photos">
    <input type="hidden" name="private" value="0">

    <input 
        type="file" 
        name="files[]" 
        multiple 
        required
    >

    <button type="submit">
        Upload
    </button>
</form>