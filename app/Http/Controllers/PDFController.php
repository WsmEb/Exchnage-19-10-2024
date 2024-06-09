<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Session;
use TCPDF;
class PDFController extends Controller
{
    public function exportPDF()
    {
        
        $donnees = Session::has('data') ? Session::get('data') : [];
        $headers = Session::has('header') ? Session::get('header') : [];
        $titre = Session::has('title') ? Session::get('title') : "";
        $html = '<style>th,td{border: 1px solid black;text-align:center;}th{font-size:17px;background-color:gold;}</style><h1 style="text-align:center;">'.$titre.  '</h1>';
        $html .= '<table cellpadding="4" style="width: 100%; border-collapse: collapse;">';
        $html .= '<thead>';
        $html .= '<tr>';
        foreach ($headers as $header)
            $html .= '<th bgcolor="gray" >' . $header . '</th>';
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';
        foreach ($donnees as $donnee) {
            $html .= '<tr>';
            foreach ($donnee as $d)
                $html .= '<td>' . $d . '</td>';
            $html .= '</tr>';
        }
        
     
        $html .= '</tbody>';
        $html .= '</table>';

        // Tcpdf ==========================================================================================================
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetTitle('Mon premier document PDF');
        // $pdf->SetHeaderData(false, false, "dsfds" . ' 023', false);

        // set header and footer fonts
        $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        // set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, 2, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $pdf->AddPage();

        $pdf->writeHTML($html, true, false, true, false, '');
        return $pdf->Output('mon_document.pdf', 'I');





    }
}
