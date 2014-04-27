<?php
return [
	/* components */
	'app\components\Controller'                   	 => BASEPATH.'app/components/controller.php',
	'app\components\AdminController'           	 => BASEPATH.'app/components/adminController.php',
	'app\components\UserControl'                  	 => BASEPATH.'app/components/userControl.php',

	/* controllers */
	'app\controllers\handler\HandlerController'    	=> BASEPATH.'app/controllers/handler/handlerController.php',
	'app\controllers\site\CommonController'        	=> BASEPATH.'app/controllers/site/commonController.php',
	'app\controllers\site\PaymentController'       	=> BASEPATH.'app/controllers/site/paymentController.php',
	'app\controllers\site\ProductController'       		=> BASEPATH.'app/controllers/site/productController.php',

	/* admin controllers */
	'app\controllers\admin\CommonController'       		=> BASEPATH.'app/controllers/admin/commonController.php',
	'app\controllers\admin\UserController'        		 	=> BASEPATH.'app/controllers/admin/userController.php',
	'app\controllers\admin\CategoryController'     		=> BASEPATH.'app/controllers/admin/categoryController.php',
	'app\controllers\admin\ProductController'      		=> BASEPATH.'app/controllers/admin/productController.php',
	'app\controllers\admin\CardController'         		=> BASEPATH.'app/controllers/admin/cardController.php',
	'app\controllers\admin\TransController'        		=> BASEPATH.'app/controllers/admin/transController.php',
	'app\controllers\admin\ModuleController'       		=> BASEPATH.'app/controllers/admin/moduleController.php',
	'app\controllers\admin\ContactController'       		=> BASEPATH.'app/controllers/admin/contactController.php',
	'app\controllers\admin\OptionController'       		=> BASEPATH.'app/controllers/admin/optionController.php',

	/* models */
	'app\models\Card'         		 	=> BASEPATH.'app/models/card.php',
	'app\models\Category'      		=> BASEPATH.'app/models/category.php',
	'app\models\Contact'       		=> BASEPATH.'app/models/contact.php',
	'app\models\Module'        		=> BASEPATH.'app/models/module.php',
	'app\models\Option'        		=> BASEPATH.'app/models/option.php',
	'app\models\Product'       		=> BASEPATH.'app/models/product.php',
	'app\models\Trans'         		=> BASEPATH.'app/models/trans.php',
	'app\models\TransCard'     		=> BASEPATH.'app/models/transCard.php',
	'app\models\TransInfo'     		=> BASEPATH.'app/models/transInfo.php',
	'app\models\User'          		=> BASEPATH.'app/models/user.php',

	/* forms */
	'app\models\forms\OrderForm'      			=> BASEPATH.'app/models/forms/orderForm.php',
	'app\models\forms\ContactForm'   	 		=> BASEPATH.'app/models/forms/contactForm.php',
	'app\models\forms\LoginForm'      			=> BASEPATH.'app/models/forms/loginForm.php',
	'app\models\forms\UserForm'       			=> BASEPATH.'app/models/forms/userForm.php',
	'app\models\forms\CategoryForm'   			=> BASEPATH.'app/models/forms/categoryForm.php',
	'app\models\forms\ProductForm'    			=> BASEPATH.'app/models/forms/productForm.php',
	'app\models\forms\CardForm'       			=> BASEPATH.'app/models/forms/cardForm.php',
	'app\models\forms\ModuleForm'     			=> BASEPATH.'app/models/forms/moduleForm.php',
	'app\models\forms\OptionForm'     			=> BASEPATH.'app/models/forms/optionForm.php',
	'app\models\forms\UserSearchForm'     		=> BASEPATH.'app/models/forms/userSearchForm.php',
	'app\models\forms\CardSearchForm'     		=> BASEPATH.'app/models/forms/cardSearchForm.php',
	'app\models\forms\TransSearchForm'     		=> BASEPATH.'app/models/forms/transSearchForm.php',
	'app\models\forms\ContactReplyForm'  		=> BASEPATH.'app/models/forms/contactReplyForm.php',

	/* modules */
	'app\components\modules\payment\Payment'           		=> BASEPATH.'app/components/modules/payment/payment.php',
	'app\components\modules\notification\Notification' 		=> BASEPATH.'app/components/modules/notification/notification.php',
];