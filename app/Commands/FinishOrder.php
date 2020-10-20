<?php

namespace App\Commands;

use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use CURLFile;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Writer\Xls;

class FinishOrder extends BaseCommand
{

    function processCommand()
    {
        $this->getBot()->deleteMessage($this->user->chat_id, $this->update->getCallbackQuery()->getMessage()->getMessageId());

        $callback_data = \GuzzleHttp\json_decode($this->update->getCallbackQuery()->getData(), true);
        $order = Order::where('id', $callback_data['id'])->first();
        if ($order) {
            $array = [
                [
                    'id' => '',
                    'title' => '',
                    'amount' => '',
                    'price' => '',
                    'total_price' => ''
                ], [
                    'id' => '',
                    'title' => 'Накладная ' . $order->id . ' от ' . $order->created_at,
                    'amount' => '',
                    'price' => '',
                    'total_price' => ''
                ], [
                    'id' => '',
                    'title' => '',
                    'amount' => '',
                    'price' => '',
                    'total_price' => ''
                ],
                [
                    'id' => 'Num.',
                    'title' => 'Название',
                    'amount' => 'К-во',
                    'price' => 'Цена',
                    'total_price' => 'Сумма'
                ],
            ];

            $order_products = OrderProduct::where('order_id', $order->id)->get();
            $return_price = 0;

            foreach ($order_products as $key => $order_product) {
                $product = Product::where('id', $order_product->product_id)->first();
                $total_price = $order_product->amount * $product->price;
                $return_price += $total_price;

                $array[] = [
                    'id' => $key + 1,
                    'title' => $product->title,
                    'amount' => $order_product->amount,
                    'price' => $product->price . ' ₽',
                    'total_price' => $total_price . ' ₽'
                ];
            }

            $array[] = [
                'id' => '',
                'title' => '',
                'amount' => 'Общее к-во: ' . $order_products->count(),
                'price' => '',
                'total_price' => 'Всего: ' . $return_price . ' ₽'
            ];

            $fp = fopen('file.csv', 'w');

            foreach ($array as $fields) {
                fputcsv($fp, $fields);
            }
            fclose($fp);

            $reader = new Csv();
            $reader->setDelimiter(',');
            $reader->setEnclosure('"');
            $reader->setSheetIndex(0);
            $spreadsheet = $reader->load("file.csv");
            $writer = new Xls($spreadsheet);
            $writer->save('file.xls');
            $spreadsheet->disconnectWorksheets();
            unset($spreadsheet);

            Order::where('id', $callback_data['id'])->update([
                'status' => 'PENDING_FOR_PAYMENT'
            ]);

            $this->getBot()->sendDocument($this->user->chat_id, new CURLFile('file.xls'));
            $admin_list = explode(',', env('ADMIN_CHAT_ID_LIST'));
            foreach ($admin_list as $admin) {
                $this->getBot()->sendDocument($admin, new CURLFile('file.xls'));
            }

            unlink('file.csv');
            unlink('file.xls');
        }
    }

}