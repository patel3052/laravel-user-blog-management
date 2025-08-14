<div class="mb-3">
    <label class="form-label">Title</label>
    <input type="text" name="title" class="form-control" value="{{ old('title', $blog->title ?? '') }}">
    @error('title')
        <small class="text-danger">{{ $message }}</small>
    @enderror
</div>

<div class="mb-3">
    <label class="form-label">Description</label>
    <textarea name="description" class="form-control" rows="6">{{ old('description', $blog->description ?? '') }}</textarea>
    @error('description')
        <small class="text-danger">{{ $message }}</small>
    @enderror
</div>

<div class="mb-3">
    <label class="form-label">Tags (comma separated)</label>
    <input type="text" name="tags_input" id="tags-input" class="form-control"
        value="{{ old('tags_input', isset($blog) ? $blog->tags->pluck('name')->implode(', ') : '') }}">
    <small class="text-muted">Example: Laravel, PHP, News</small>
    @error('tags_input')
        <small class="text-danger">{{ $message }}</small>
    @enderror
</div>

{{-- <div class="mb-3">
    <label class="form-label">Tags (comma separated)</label>
    <input type="text" name="tags_input" class="form-control"
        value="{{ old('tags_input', isset($blog) ? $blog->tags->pluck('name')->implode(', ') : '') }}">
    <small class="text-muted">Example: Laravel, PHP, News</small>
    @error('tags_input')
        <small class="text-danger">{{ $message }}</small>
    @enderror
</div> --}}

<div class="mb-3">
    <label class="form-label">Blog Images (multiple)</label>
    <input type="file" name="images[]" class="form-control" accept="image/*" multiple id="images-input">
    @error('images.*')
        <small class="text-danger">{{ $message }}</small>
    @enderror
    <div class="mt-2 d-flex gap-2 flex-wrap" id="preview"></div>

    @if (isset($blog) && $blog->images->count())
        <div class="mt-3">
            <div class="fw-bold mb-1">Existing Images</div>
            <div class="d-flex gap-2 flex-wrap">
                @foreach ($blog->images as $img)
                    <div class="position-relative">
                        <img src="{{ asset('storage/' . $img->path) }}" width="120" class="border rounded">
                        <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0"
                            onclick="removeImage({{ $img->id }}, this)">×</button>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

<div class="mb-3">
    <label class="form-label">Links</label>
    <div id="links-wrapper">
        @php
            $oldTitles = old('link_titles', isset($blog) ? $blog->links->pluck('title')->toArray() : []);
            $oldUrls = old('link_urls', isset($blog) ? $blog->links->pluck('url')->toArray() : []);
            $count = max(1, count($oldTitles));
        @endphp

        @for ($i = 0; $i < $count; $i++)
            <div class="row g-2 align-items-end mb-2 link-row">
                <div class="col-md-5">
                    <label class="form-label">Link Title</label>
                    <input type="text" name="link_titles[]" class="form-control" value="{{ $oldTitles[$i] ?? '' }}">
                </div>
                <div class="col-md-5">
                    <label class="form-label">URL</label>
                    <input type="url" name="link_urls[]" class="form-control" value="{{ $oldUrls[$i] ?? '' }}">
                </div>
                <div class="col-md-2 d-flex gap-1">
                    <button type="button" class="btn btn-secondary add-row">+</button>
                    <button type="button" class="btn btn-danger remove-row">−</button>
                </div>
            </div>
        @endfor
    </div>
    @error('links')
        <small class="text-danger">{{ $message }}</small>
    @enderror
</div>

<script>
    let dt = new DataTransfer();

    document.getElementById('images-input')?.addEventListener('change', function(e) {
        const box = document.getElementById('preview');

        [...e.target.files].forEach(file => {
            // Add new file to DataTransfer object
            dt.items.add(file);

            // Create image preview
            const imgWrapper = document.createElement('div');
            imgWrapper.style.position = 'relative';
            imgWrapper.style.display = 'inline-block';
            imgWrapper.style.margin = '5px';

            const img = document.createElement('img');
            img.src = URL.createObjectURL(file);
            img.width = 120;
            img.className = 'border rounded';

            const removeBtn = document.createElement('button');
            removeBtn.innerHTML = '×';
            removeBtn.type = 'button';
            removeBtn.className = 'btn btn-sm btn-danger position-absolute top-0 end-0';
            removeBtn.onclick = function() {
                for (let i = 0; i < dt.items.length; i++) {
                    if (dt.items[i].getAsFile().name === file.name) {
                        dt.items.remove(i);
                        break;
                    }
                }
                // Update input files
                document.getElementById('images-input').files = dt.files;
                imgWrapper.remove();
            };

            imgWrapper.appendChild(img);
            imgWrapper.appendChild(removeBtn);
            box.appendChild(imgWrapper);
        });

        e.target.files = dt.files;
    });

    // dynamic link rows with delete
    document.getElementById('links-wrapper')?.addEventListener('click', function(e) {
        if (e.target.classList.contains('add-row')) {
            const row = e.target.closest('.link-row');
            const clone = row.cloneNode(true);
            clone.querySelectorAll('input').forEach(i => i.value = '');
            row.after(clone);
        }
        if (e.target.classList.contains('remove-row')) {
            const allRows = document.querySelectorAll('#links-wrapper .link-row');
            if (allRows.length > 1) {
                e.target.closest('.link-row').remove();
            } else {
                alert('You must have at least one link row.');
            }
        }
    });

    // remove existing image (AJAX)
    function removeImage(id, btn) {
        if (!confirm('Remove this image?')) return;
        fetch('{{ url('blogs/image') }}/' + id, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        }).then(r => r.json()).then(res => {
            if (res.status) {
                btn.closest('.position-relative').remove();
            }
        });
    }

    // Initialize Tagify on tags input
    document.addEventListener('DOMContentLoaded', function() {
        var input = document.querySelector('#tags-input');
        if (input) {
            new Tagify(input, {
                delimiters: ",", // use comma as separator
                maxTags: 15,
                dropdown: {
                    enabled: 0 // disable suggestions dropdown
                }
            });
        }
    });
</script>
