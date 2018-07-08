<?php
/**
 * Created by PhpStorm.
 * Author: houpeng
 * DateTime: 2017/04/15 11:30
 * Description:
 */
namespace nolanchic\swoole\controllers;

use nolanchic\swoole\models\Test;
use yii\web\Controller;
use Yii;


class DefaultController extends Controller
{
    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $data = [
            "data"=>[
                [
                    "a" => "http://域名/swoole/default/test",
                ],
                [
                    "a" => "http://域名/swoole/default/test",
                ],
                [
                    "a" => "http://域名/swoole/default/test",
                ],

            ],
        ];
        Yii::$app->swoole->webTask($data);
        echo '执行成功';
    }

    public function actionMongo(){
        $data = [
            "data"=>[
                [
                    "a" => "test/insert",
                ],
                [
                    "a" => "test/insert",
                ],
                [
                    "a" => "test/insert",
                ],

            ],
        ];
        Yii::$app->swoole->mongodbTask($data);
        echo '执行成功';

    }

    public function actionDel(){
        $data = [
            "data"=>[
                [
                    "a" => "test/del",
                ],

            ],
        ];
        Yii::$app->swoole->mongodbTask($data);
        echo '执行成功';
    }

    public function actionPush(){
        $data = [
            "data"=>'hello world',
        ];
        $fd = 1;
        Yii::$app->swoole->pushMsg($fd,$data);
        echo '执行成功';
    }

    public function actionTest(){
        $test = new Test();
        $testInfo = $test::findOne(['a'=>'hello']);
        $collection = Yii::$app->mongodb->getCollection ('test');
        if(empty($testInfo)){
            $collection->insert ( [
                'a'=>'hello',
                'b'=>'wrold',
                'status'=>$test::NORMAL,
                'addtime'=>date('Y-m-d h:i:s',time())
            ] );
        }else{
            for($i=0;$i<1000;$i++){
                $collection->insert ( [
                    'a'=>'hi',
                    'b'=>'wrold',
                    'status'=>$test::NORMAL,
                    'addtime'=>date('Y-m-d h:i:s',time())
                ] );
            }
        }

        echo '执行成功';
    }
    
}
