<?php

namespace App\Http\Controllers;
use App\Models\Buku;
use App\Http\Requests\BukuStoreRequest;

class BukuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $buku = Buku::all();
        return view('buku.index', ['buku' => $buku]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('buku.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BukuStoreRequest $request)
    {
        Buku::create($request->validated());

        return redirect()->route('admin.buku.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Buku $buku)
    {
        return view('buku.edit', ['buku' => $buku]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BukuStoreRequest $request, Buku $buku)
    {
        $buku->update($request->validated());

        return redirect()->route('admin.buku.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Buku $buku)
    {
        $buku->delete();
        return redirect()->route('admin.buku.index');
    }
}
