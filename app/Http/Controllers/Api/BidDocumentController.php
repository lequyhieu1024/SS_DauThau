<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\BidDocumentRepository;
use Illuminate\Http\Request;

class BidDocumentController extends Controller
{

    public $bidDocumentRepository;

    public function __construct(BidDocumentRepository $bidDocumentRepository)
    {
        $this->middleware(['permission:list_bid_document'])->only('index');
        $this->middleware(['permission:create_bid_document'])->only(['store']);
        $this->middleware(['permission:update_bid_document'])->only(['update']);
        $this->middleware(['permission:detail_bid_document'])->only('show');
        $this->middleware(['permission:destroy_bid_document'])->only('destroy');
        $this->bidDocumentRepository = $bidDocumentRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
