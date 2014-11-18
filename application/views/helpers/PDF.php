<?php
require_once APPLICATION_PATH . '/../library/mpdf/mpdf.php';

class Zend_View_Helper_PDF extends Zend_View_Helper_Abstract
{
	public function pdf($html, $filename)
	{
		// build filename
		// {last_name}_{first_name}_{seminar_date}.pdf
		$location = APPLICATION_PATH . '/../data/reports/' . $filename;
		$mpdf=new mPDF(
			'en-GB-x',
			'Letter'
		);
		$mpdf->shrink_tables_to_fit=1;
		$mpdf->WriteHTML($html);
		
		$mpdf->Output(
			$location,
			'F'
		);

		echo $this->view->baseUrl() . "/grading/download-report/$filename";

	}

}
