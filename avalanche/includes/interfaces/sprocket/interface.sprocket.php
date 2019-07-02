<?

interface avalanche_interface_sprocket {

   ///////////////////////////////////////
   // returns HTML content for the thin //
   // representation of this sprocket   //
   ///////////////////////////////////////
   public function getThinContent($skin);

   ///////////////////////////////////////
   // returns HTML content for the wide //
   // representation of this sprocket   //
   ///////////////////////////////////////
   public function getWideContent($skin);

   ///////////////////////////////////////
   // returns HTML content for the link //
   // to the window version of this	//
   // sprocket				//
   ///////////////////////////////////////
   public function getWindowContent($skin);
}


?>