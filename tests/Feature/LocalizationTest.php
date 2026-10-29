<?php

use Illuminate\Support\Facades\Validator;

it('uses Indonesian as the application locale', function (): void {
    expect(app()->getLocale())->toBe('id');
});

it('renders validation messages in Indonesian', function (): void {
    $validator = Validator::make([], ['nama' => 'required']);

    expect($validator->errors()->first('nama'))->toBe('Kolom nama wajib diisi.');
});
