<?php

namespace App\Http\Controllers;

use App\Models\EmailList;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class EmailListController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('email-list.index',[
            'emailLists' => EmailList::query()->paginate()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('email-list.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => ['required', 'max:255'],
            'file' => ['required', 'file', 'mimes:csv']
        ]);

        $emails = $this->getEmailsFromCsvFile($request->file('file'));

        DB::transaction(function() use ($request, $emails) {
            $emailList = EmailList::query()->create(['title' => $request->title]);
            $emailList->subscribers()->createMany($emails);
        });

        return to_route('email-list.index');
    }

    public function getEmailsFromCsvFile(UploadedFile $file):array {
        $fileHandle = fopen($file->getRealPath(), 'r');
        $emails = [];

        while (($row = fgetcsv($fileHandle, null, ',')) !== false) {
            if ($row[0] == 'Name' && $row[1] == 'Email') {
                continue;
            }
            $emails[] = [
                'name' => $row[0],
                'email' => $row[1]
            ];
        }

        fclose($fileHandle);

        return $emails;
    }

    /**
     * Display the specified resource.
     */
    public function show(EmailList $emailList)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EmailList $emailList)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, EmailList $emailList)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EmailList $emailList)
    {
        //
    }
}
