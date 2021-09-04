<?php
namespace app\api\modules\v1\controllers;

use Yii;
use yii\filters\AccessControl;
use app\behaviours\Verbcheck;
use app\behaviours\Apiauth;
use app\components\BaseController;
use app\models\Project;
use app\models\Constant;

class ProjectController extends BaseController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {

        $behaviors = parent::behaviors();

        return $behaviors + [
            'apiauth' => [
                'class' => Apiauth::class,
                'exclude' => ['index'],
            ],
            'access' => [
                'class' => AccessControl::class,
                'only' => [],
                'rules' => [
                    [
                        'actions' => [],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['index', 'create', 'get-item'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => Verbcheck::class,
                'actions' => [
                    'create' => ['POST'],
                    'get-item' => ['GET'],
                    'index' => ['GET'],
                ],
            ],
        ];
    }

    /**
     * @SWG\Get(
     *     path="/project",
     *     summary="Get all projects",
     *     description="A list of projects",
     *     produces={"application/json"},
     *     consumes={"application/x-www-form-urlencoded"},
     *     tags={"Projects"},
     *     @SWG\Parameter(
     *         type="string",
     *         name="Authorization",
     *         in="header",
     *         description="Bearer + token",
     *         required=true
     *     ),
     *     @SWG\Parameter(
     *         type="integer",
     *         name="page",
     *         in="query",
     *         minimum=1,
     *         description="Number of pages in the collection to return",
     *         required=false
     *     ),
     *     @SWG\Parameter(
     *         type="integer",
     *         name="limit",
     *         in="query",
     *         minimum=10,
     *         maximum=100,
     *         description="Number of projects to return",
     *         required=false
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="OK",
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Bad Request",
     *     )
     * )
     */
    public function actionIndex()
    {

        $limit = Yii::$app->request->getQueryParam('limit', Constant::LIMIT);
        if (!is_numeric($limit)) $limit = Constant::LIMIT;

        $page = Yii::$app->request->getQueryParam('page', 1);
        if (!is_numeric($page)) $page = 1;

        $offset = (abs($page) - 1) * $limit;


        $model = Project::find()->limit($limit)->offset($offset)->all();

        Yii::$app->api->sendSuccessResponse($model);
    }

    /**
     * @SWG\Get(
     *     path="/project/{id}",
     *     summary="Get a project by ID",
     *     description="Get information a project",
     *     produces={"application/json"},
     *     consumes={"application/x-www-form-urlencoded"},
     *     tags={"Projects"},
     *     @SWG\Parameter(
     *         type="string",
     *         name="Authorization",
     *         in="header",
     *         description="Bearer + token",
     *         required=true
     *     ),
     *     @SWG\Parameter(
     *         type="integer",
     *         name="id",
     *         in="path",
     *         description="Numeric ID of the project to get",
     *         required=true
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="OK",
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Bad Request",
     *     )
     * )
     */
    public function actionGetItem()
    {
        $id = Yii::$app->request->getQueryParam('id');
        if ($id === null) {
            $message = Yii::t('app', 'Id invalid');
            Yii::$app->api->sendFailedResponse($message);
        }

        $model = Project::findOne(["id" => $id]);
        if ($model === null) {
            $message = Yii::t('app', 'Project Id is not exist');
            Yii::$app->api->sendFailedResponse($message);
        }

        Yii::$app->api->sendSuccessResponse($model->attributes);
    }

    /**
     * @SWG\Post(
     *     path="/project/create",
     *     summary="Create a new project",
     *     produces={"application/json"},
     *     consumes={"application/x-www-form-urlencoded"},
     *     tags={"Projects"},
     *     description="The api create a new project",
     *     @SWG\Parameter(
     *         type="string",
     *         name="Authorization",
     *         in="header",
     *         description="Bearer + token",
     *         required=true
     *     ),
     *     @SWG\Parameter(
     *         name="name",
     *         in="formData",
     *         type="string",
     *         description="Project name",
     *         required=true,
     *     ),
     *     @SWG\Parameter(
     *         name="alias",
     *         in="formData",
     *         type="string",
     *         description="Project alias",
     *         required=true,
     *     ),
     *     @SWG\Parameter(
     *         name="status",
     *         in="formData",
     *         type="string",
     *         description="0 | 1 | 2 | 3",
     *         required=false,
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="OK",
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Bad Request",
     *     )
     * )
     */
    public function actionCreate() {
        $model = new Project();
        $model->attributes = Yii::$app->request->post();
        if (!$model->validate())
        {
            $errors = $model->getFirstErrors();
            $message = reset($errors);
            Yii::$app->api->sendFailedResponse($message);
        }

        if (!isset($model->status))
        {
            $model->status = 0;
        }

        if ($model->save())
        {
            $data = $model->attributes;
            Yii::$app->api->sendSuccessResponse($data);
        }
    }
}