<div class="card mb-6">
    <!-- Account -->
    <div class="card-body pt-4">
        <form method="POST" action="{{ route('profile.update-teacher') }}"
            enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="mb-4 col-md-12">
                    <label for="qualifications" class="form-label">{{ __('Qualifications') }}</label><span
                        class="text-danger">*</span>
                    <input class="form-control @error('qualifications') is-invalid @enderror" type="text"
                        id="qualifications" name="qualifications" value="{{ $profile->qualifications }}"
                        placeholder="{{ __('Enter your qualifications') }}" autofocus required />
                    @error('qualifications')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="mb-4 col-md-12">
                    <label class="form-label" for="subject_id">{{ __('Subjects') }}</label><span
                        class="text-danger">*</span>
                    <select id="subject_id" name="subject_id[]" multiple
                        class="select2 form-select @error('subject_id.*') is-invalid @enderror">
                        @foreach ($subjects as $subject)
                            <option value="{{ $subject->id }}"
                                {{ in_array($subject->id, old('subject_id', $teacherSubjects ?? [])) ? 'selected' : '' }}>
                                {{ $subject->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('subject_id.*')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

            </div>
            <div class="mt-2">
                <button type="submit" class="btn btn-primary me-3">{{ __('Save changes') }}</button>
            </div>
        </form>
    </div>
    <!-- /Account -->
</div>
