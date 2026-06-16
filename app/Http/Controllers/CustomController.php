<?php

namespace App\Http\Controllers;

use App\Models\CancerDiagnose;
use Illuminate\Contracts\View\View;

class CustomController extends Controller
{
    public function showForm(CancerDiagnose $cancerDiagnose): View
    {
        $cancerDiagnose->loadMissing('formDemographic');

        $formDetails = $cancerDiagnose->formDemographic;

        abort_if($formDetails === null, 404, 'Form not found');

        return view('form', [
            'cancerDiagnose' => $cancerDiagnose,
            'formDetails' => $formDetails,
        ]);
    }
}
