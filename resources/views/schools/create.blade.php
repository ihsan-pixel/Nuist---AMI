<x-app-layout>
    <h1 class="mb-6 text-2xl font-semibold">Tambah Sekolah</h1>
    <form method="POST" action="{{ route('admin.schools.store') }}" class="grid gap-4 rounded-2xl bg-white p-6 shadow-sm md:grid-cols-2">
        @csrf
        <input name="scod" placeholder="SCOD" class="rounded-xl border p-3" />
        <input name="name" placeholder="Nama Sekolah" class="rounded-xl border p-3" />
        <input name="education_level" placeholder="Jenjang" class="rounded-xl border p-3" />
        <input name="district" placeholder="Kabupaten" class="rounded-xl border p-3" />
        <input name="email" placeholder="Email" class="rounded-xl border p-3" />
        <input name="phone" placeholder="Telepon" class="rounded-xl border p-3" />
        <textarea name="address" placeholder="Alamat" class="rounded-xl border p-3 md:col-span-2"></textarea>
        <select name="status" class="rounded-xl border p-3 md:col-span-2">
            <option value="active">active</option>
            <option value="inactive">inactive</option>
        </select>
        <button class="rounded-xl bg-[#00553F] px-4 py-2 text-white md:col-span-2">Simpan</button>
    </form>
</x-app-layout>
