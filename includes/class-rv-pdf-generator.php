<?php


class RV_PDF_Generator
{
  public function generate($formid, $entryid, $args = array())
	{

      ob_start();
      RVFPDF()->template_loader->get_template_part( 'default' ,'pdf',true );
      $out = ob_get_contents();
      ob_end_clean();

      $mpdf = new \Mpdf\Mpdf();
      $mpdf->WriteHTML($out);

      $updir = RVFPDF_UPLOAD_DIR.'/'.$formid;

      if (!is_dir($updir) ) {
         mkdir( $updir, 0700 );
      }

      $filename = $updir.'/'.$entryid.'.pdf';

      if ( isset( $args['download'] ) && $args['download'] ) {
        //$mpdf->Output();
        $mpdf->Output($filename, \Mpdf\Output\Destination::FILE);
        $mpdf->Output($entryid.'.pdf', \Mpdf\Output\Destination::DOWNLOAD);
      }else{
        $mpdf->Output();
      }

  }
}
