<?php
// +---------------------------------------------------------------------+
// | OneBase    | [ WE CAN DO IT JUST THINK ]                            |
// +---------------------------------------------------------------------+
// | Licensed   | http://www.apache.org/licenses/LICENSE-2.0 )           |
// +---------------------------------------------------------------------+
// | Author     | Bigotry <3162875@qq.com>                               |
// +---------------------------------------------------------------------+
// | Repository | https://gitee.com/Bigotry/OneBase                      |
// +---------------------------------------------------------------------+

/**
 * 应用公共扩展（函数）文件
 */

/**
 * 导出excel信息
 * @param string  $titles    导出的表格标题
 * @param string  $keys      需要导出的键名
 * @param array   $data      需要导出的数据
 * @param string  $file_name 导出的文件名称
 */
function export_excel($titles = '', $keys = '', $data = [], $file_name = '导出文件' )
{
    
    $objPHPExcel = get_excel_obj($file_name);
        
    $y = 1;
    $s = 0;

    $titles_arr = str2arr($titles);

    foreach ($titles_arr as $k => $v) {
        $objPHPExcel->setActiveSheetIndex($s)->getColumnDimension(string_from_column_index($k))->setAutoSize(true);   //内容自适应
        $objPHPExcel->setActiveSheetIndex($s)->setCellValue(string_from_column_index($k). $y, $v);
    }

    $keys_arr = str2arr($keys);

//    foreach ($data as $k => $v)
//    {
//
//        is_object($v) && $v = $v->toArray();
//
//        foreach ($v as $kk => $vv)
//        {
//
//            $num = array_search($kk, $keys_arr);
//
//            false !== $num && $objPHPExcel->setActiveSheetIndex($s)->setCellValue(string_from_column_index($num) . ($y + $k + 1), $vv );
//        }
//    }

        foreach ($data as $k => $v)
        {
            foreach ($keys_arr as $kk=>$vv){
                $value = preg_replace("/<\/?[^>]+>/i", '', $v[$vv]);//去掉html标签
                $objPHPExcel->setActiveSheetIndex($s)->setCellValue(string_from_column_index($kk) . ($y + $k + 1), $value );
            }

        }

        //设置边框
        $sharedStyle1 = new PHPExcel_Style();
        $sharedStyle1->applyFromArray(array(
            'borders' => array(
                'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'left' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
            )
        ));
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1, "A1:".string_from_column_index($kk). ($y + $k + 1));

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');//excel2003
    
    $objWriter->save('php://output'); exit;
}

/**
 * 获取excel
 */
function get_excel_obj($file_name = '导出文件')
{
    
    set_time_limit(0);

    vendor('phpoffice/phpexcel/Classes/PHPExcel');

    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
    header("Content-Type:application/force-download");
    header("Content-Type:application/vnd.ms-execl");
    header("Content-Type:application/octet-stream");
    header("Content-Type:application/download");
    header('Content-Disposition:attachment;filename='.iconv("utf-8", "gb2312", $file_name).'.xls');
    header("Content-Transfer-Encoding:binary");
    
    return new PHPExcel();
}

/**
 * 读取excel返回数据
 */
function get_excel_data($file_url = '', $start_row = 1, $start_col = 0)
{

    vendor('phpoffice/phpexcel/Classes/PHPExcel');

    $objPHPExcel        = PHPExcel_IOFactory::load($file_url);
    $objWorksheet       = $objPHPExcel->getActiveSheet();
    $highestRow         = $objWorksheet->getHighestDataRow(); 
    $highestColumn      = $objWorksheet->getHighestDataColumn(); 
    $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn); 
    
    $excel_data = [];
    
    for ($row = $start_row; $row <= $highestRow; $row++)
    {
        for ($col = $start_col; $col < $highestColumnIndex; $col++)
        {
            $excel_data[$row][] =(string)$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
        }
    }

    return $excel_data;
}

/**
 * 数字转字母
 */
function  string_from_column_index($pColumnIndex = 0)
{
    static $_indexCache = [];
    
    if (!isset($_indexCache[$pColumnIndex])) {
        
        if ($pColumnIndex < 26) {
            
            $_indexCache[$pColumnIndex] = chr(65 + $pColumnIndex);
        } elseif ($pColumnIndex < 702) {
            
            $_indexCache[$pColumnIndex] = chr(64 + ($pColumnIndex / 26)).chr(65 + $pColumnIndex % 26);
        } else {
            
            $_indexCache[$pColumnIndex] = chr(64 + (($pColumnIndex - 26) / 676 )).chr(65 + ((($pColumnIndex - 26) % 676) / 26 )).  chr( 65 + $pColumnIndex % 26);
        }
    }
    
    return $_indexCache[$pColumnIndex];
}

/**
 * 发送邮件
 */
function send_email($address, $title, $message)
{
    
    $mail = new \ob\PHPMailer();
    
    $mail->isSMTP();
    $mail->Host="smtp.exmail.qq.com";
    $mail->SMTPAuth = true;
    $mail->Username="";
    $mail->Password="";
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;
    $mail->CharSet='UTF-8';
    $mail->setFrom('', '');
    $mail->addAddress($address);
    $mail->isHTML(true);
    $mail->Subject = $title;
    $mail->Body  = $message;
    $mail->AltBody = 'OneBase';
    
    if (!$mail->send()) { return $mail->ErrorInfo; }
    
    return true;
}

/**
 * 生成条形码
 * @param string $text      写入内容
 * @param string $file_name 文件名称
 * @param string $path      条形码保存路径
 * @param string $codebar   条形码类型
 * 'BCGcodabar','BCGcode11','BCGcode39','BCGcode39extended','BCGcode93',
 * 'BCGcode128','BCGean8','BCGean13','BCGisbn','BCGi25','BCGs25','BCGmsi',
 * 'BCGupca','BCGupce','BCGupcext2','BCGupcext5','BCGpostnet','BCGothercode'
 */
function create_barcode($text = '', $file_name = '', $path = '', $codebar = 'BCGcode39')
{
    
    $class_path = EXTEND_PATH . 'barcode' . DS . 'class' . DS;
    
    include_once $class_path . 'BCGFont.php';
    include_once $class_path . 'BCGColor.php';
    include_once $class_path . 'BCGDrawing.php';
    include_once $class_path . $codebar . '.barcode.php';
    
    // The arguments are R, G, B for color.
    $color_black = new BCGColor(0, 0, 0);
    $color_white = new BCGColor(255, 255, 255); 

    $code = new $codebar();
    $code->setScale(2); // Resolution
    $code->setThickness(25); // Thickness
    $code->setForegroundColor($color_black); // Color of bars
    $code->setBackgroundColor($color_white); // Color of spaces
    $code->setFont(new BCGFont($class_path . 'font/Arial.ttf', 10)); // Font (or 0)
    $code->parse($text); 
    
    /*  
     *  Here is the list of the arguments
     *  1 - Filename (empty : display on screen)
     *  2 - Background color 
     */
    
    // $drawing = new BCGDrawing($_REQUEST['file_name'], $color_white);

    $file_name=  empty($file_name)?md5($text.$codebar).'.png':md5($file_name.$codebar).'.png';

    $save_path = empty($path) ? PATH_UPLOAD . 'extend' . DS . 'barcode' . DS : $path;
    if (!file_exists($save_path)) { mkdir($save_path,'0777',true); }
    $drawing = new BCGDrawing($save_path . $file_name , $color_white);
    $drawing->setBarcode($code);
    $drawing->draw();

    // Header that says it is an image (remove it if you save the barcode to a file)
    // header('Content-Type: image/png');
    // Draw (or save) the image into PNG format.
    $drawing->finish(BCGDrawing::IMG_FORMAT_PNG);
    
    return ['name' => $file_name, 'path' => $save_path . $file_name ,'relativePath'=>str_replace(PATH_PUBLIC,DS,$save_path).$file_name];
}

/**
 * 生成二维码
 * @param string $data      写入数据
 * @param string $path      二维码保存路径
 * @param string $logo      准备好的logo图片路径+文件名,
 * @param string $ecc       错误修正水平    'L','M','Q','H'
 * @param int    $size      二维码大小      1 - 10
 */
function create_qrcode($data = '', $path = '',$logo='', $ecc = 'H', $size = 10)
{
    
    $save_path = empty($path) ? PATH_UPLOAD . 'extend' . DS . 'qrcode' . DS : $path;
    
    include_once EXTEND_PATH . 'qrcode' . DS . 'qrlib.php';

    if (!file_exists($save_path)) { mkdir($save_path,'0777',true); }
    
    $filename = $save_path.'.png';
    
    $errorCorrectionLevel = 'L';
    
    if (isset($ecc) && in_array($ecc, array('L','M','Q','H'))) { $errorCorrectionLevel = $ecc; } 
    
    $matrixPointSize = 4;
    
    if (isset($size)) { $matrixPointSize = min(max((int)$size, 1), 10); }
    
    if (isset($data)) {
        
        if (trim($data) == '') {  exception("qrcode data cannot be empty"); }
        
        $filename = $save_path.md5($data.'|'.$errorCorrectionLevel.'|'.$matrixPointSize).'.png';
        
        QRcode::png($data, $filename, $errorCorrectionLevel, $matrixPointSize, 2);
        
    }else{
        
        QRcode::png('PHP QR Code :)', $filename, $errorCorrectionLevel, $matrixPointSize, 2);    
    }

    //logo图片存在
    if (!empty($logo)) {
        $qrcode = imagecreatefromstring(file_get_contents($filename));
        $logo = imagecreatefromstring(file_get_contents($logo));
        if (imageistruecolor($logo))
        {
            imagetruecolortopalette($logo, false, 65535);//添加这行代码来解决颜色失真问题
        }
        $qrcode_width = imagesx($qrcode);   //二维码图片宽度
        $qrcode_height = imagesy($qrcode);  //二维码图片高度
        $logo_width = imagesx($logo);       //logo图片宽度
        $logo_height = imagesy($logo);      //logo图片高度
        $logo_qr_width = $qrcode_width / 5;
        $scale = $logo_width / $logo_qr_width;
        $logo_qr_height = $logo_height / $scale;
        $from_width = ($qrcode_width - $logo_qr_width) / 2;
        //重新组合图片并调整大小
        imagecopyresampled($qrcode, $logo, $from_width, $from_width, 0, 0, $logo_qr_width,
            $logo_qr_height, $logo_width, $logo_height);
    }
    //输出图片
    imagepng($qrcode, $filename);




    $name = basename($filename);
    
    $return_data['name'] = $name;
    $return_data['path'] = $save_path . $name;
    $return_data['relativePath'] = str_replace(PATH_PUBLIC,DS,$save_path) . $name;

    return $return_data;
    
}