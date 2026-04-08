<div class="grid grid-cols-1 md:grid-cols-2 gap-5">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Bank Name</label>
        <input type="text" name="bank_name"
               value="{{ old('bank_name', $bankAccount->bank_name ?? '') }}"
               class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
               required>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Account Title</label>
        <input type="text" name="account_title"
               value="{{ old('account_title', $bankAccount->account_title ?? '') }}"
               class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
               required>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Account Number</label>
        <input type="text" name="account_number"
               value="{{ old('account_number', $bankAccount->account_number ?? '') }}"
               class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
               required>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">IBAN</label>
        <input type="text" name="iban"
               value="{{ old('iban', $bankAccount->iban ?? '') }}"
               class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Branch Name</label>
        <input type="text" name="branch_name"
               value="{{ old('branch_name', $bankAccount->branch_name ?? '') }}"
               class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Branch Code</label>
        <input type="text" name="branch_code"
               value="{{ old('branch_code', $bankAccount->branch_code ?? '') }}"
               class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
    </div>
</div>

<div>
    <label class="inline-flex items-center gap-2 mt-4">
        <input type="checkbox" name="is_active" value="1"
               class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
               {{ old('is_active', isset($bankAccount) ? $bankAccount->is_active : true) ? 'checked' : '' }}>
        <span class="text-sm text-gray-700">Active Bank Account</span>
    </label>
</div>