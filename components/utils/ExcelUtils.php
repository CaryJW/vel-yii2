<?php

namespace app\components\utils;

use Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

/**
 * excel工具
 *
 * @author Cary
 * @date 2021/9/29
 */
class ExcelUtils
{
    /**
     * 导出excel
     * @throws Exception
     */
    static function export($fileName, $title, $data)
    {
        if (count($title) <= 0) {
            return;
        }

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();

        //设置工作表标题名称
        $worksheet->setTitle('模板');
        //表头 设置单元格内容
        foreach (array_values($title) as $key => $value) {
            $k = $key + 1;
            $worksheet->setCellValueByColumnAndRow($k, 1, $value);
        }

        $row = 2; //从第二行开始
        foreach ($data as $item) {
            $column = 1; //从第一列设置并初始化
            foreach (array_keys($title) as $key) {
                $value = isset($item[$key]) ? $item[$key] : '';
                $worksheet->setCellValueByColumnAndRow($column, $row, $value); //哪一列哪一行设置哪个值
                $column++; //列数加1
            }
            $row++; //行数加1
        }

        // 写入到临时文件
        $file = VelFileUtils::getTempFile($fileName);
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($file);
        // 下载
        VelFileUtils::download($file);
    }
}