<?php
require_once APPLICATION_PATH . '/../library/mpdf/mpdf.php';

class Zend_View_Helper_PDF extends Zend_View_Helper_Abstract
{
	public function pdf($html, $filename)
	{
		// build filename
		// {last_name}_{first_name}_{seminar_date}.pdf
		$location = APPLICATION_PATH . '/../library/reports/' . $filename;
		$mpdf=new mPDF(
			'en-GB-x',
			'Letter'
		);
		$mpdf->WriteHTML($html);
		
		$mpdf->Output(
			$location
		);

		echo "/grading/download-report/$filename";

	}

}
