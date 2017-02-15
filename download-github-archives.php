function dgarch($repo=false, $dir=false, $temp=false, $rmarch=true)
{
  /*
    $repo   -> Repository link
    $dir    -> Directory to extract archive files
    $temp   -> Temp dir to store archive
    $rmarch -> Downloaded archive removing (if setted "true")
  */
  if(!$repo || !$dir) return false;
  if(!$temp) $temp = $_SERVER['DOCUMENT_ROOT']."/temp";
  if(!file_exists($temp)) mkdir($temp, 0777, true);

  $file = $temp."/".basename($repo);
  $git = file_get_contents($repo);
  file_put_contents($file, $git);
  if(!file_exists($file)) return false;

  $zip = new ZipArchive;
  $res = $zip->open($file)[0];
  $gfolder = basename($zip->statIndex(0)['name']);

  for($i = 0; $i < $zip->numFiles; $i++)
  {
    $filename = $zip->getNameIndex($i);
    $fileinfo = pathinfo($filename);
    $pp = explode("/", $fileinfo['dirname']);
    if($pp[0] == $gfolder || $pp[0] == ".")
      unset($pp[0]);

    $path = $dir."/";

    if(!empty($pp))
      $path = $dir."/".implode("/", $pp);

    if(!file_exists($path))
      mkdir($path, 777, true);

    if(@explode(".", $filename)[1])
      copy("zip://".$file."#".$filename, $path."/".$fileinfo['basename']);
  }
  $zip->close();
  if($rmarch) unlink($file);
}
