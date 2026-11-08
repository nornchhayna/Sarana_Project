@php
    $isEdit = isset($supplier) && $supplier->exists;
@endphp

<div class="card card-primary card-outline">
    <div class="card-body">
        <div class="row">
            <div class="form-group col-md-6">
                <label for="name">Name <span class="text-danger">*</span></label>
                <input type="text" name="name" id="name"
                       value="{{ old('name', $supplier->name ?? '') }}"
                       class="form-control @error('name') is-invalid @enderror">
                @error('name')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group col-md-6">
                <label for="contact_person">Contact Person</label>
                <input type="text" name="contact_person" id="contact_person"
                       value="{{ old('contact_person', $supplier->contact_person ?? '') }}"
                       class="form-control @error('contact_person') is-invalid @enderror">
                @error('contact_person')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="row">
            <div class="form-group col-md-6">
                <label for="phone">Phone</label>
                <input type="text" name="phone" id="phone"
                       value="{{ old('phone', $supplier->phone ?? '') }}"
                       class="form-control @error('phone') is-invalid @enderror">
                @error('phone')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group col-md-6">
                <label for="email">Email</label>
                <input type="email" name="email" id="email"
                       value="{{ old('email', $supplier->email ?? '') }}"
                       class="form-control @error('email') is-invalid @enderror">
                @error('email')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="form-group">
            <label for="address">Address</label>
            <input type="text" name="address" id="address"
                   value="{{ old('address', $supplier->address ?? '') }}"
                   class="form-control @error('address') is-invalid @enderror">
            @error('address')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <div class="row">
            <div class="form-group col-md-6">
                <label for="city">City</label>
                <input type="text" name="city" id="city"
                       value="{{ old('city', $supplier->city ?? '') }}"
                       class="form-control @error('city') is-invalid @enderror">
                @error('city')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group col-md-6">
                <label for="country">Country</label>
                <input type="text" name="country" id="country"
                       value="{{ old('country', $supplier->country ?? '') }}"
                       class="form-control @error('country') is-invalid @enderror">
                @error('country')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="form-group">
            <label for="notes">Notes</label>
            <textarea name="notes" id="notes" rows="3"
                      class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $supplier->notes ?? '') }}</textarea>
            @error('notes')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group form-check">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" id="is_active" value="1" class="form-check-input"
                   @checked(old('is_active', $supplier->is_active ?? true))>
            <label class="form-check-label" for="is_active">Active</label>
        </div>
    </div>
</div>

<div class="mb-4">
    <button type="submit" class="btn btn-primary">
        <i class="fas fa-save"></i> {{ $isEdit ? 'Update' : 'Create' }} Supplier
    </button>
    <a href="{{ $isEdit ? route('admin.suppliers.show', $supplier) : route('admin.suppliers.index') }}" class="btn btn-default">
        Cancel
    </a>
</div>
