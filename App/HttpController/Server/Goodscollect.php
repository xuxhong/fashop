<?php
/**
 *
 * Copyright  FaShop
 * License    http://www.fashop.cn
 * link       http://www.fashop.cn
 * Created by FaShop.
 * User: hanwenbo
 * Date: 2018/4/3
 * Time: 下午12:00
 *
 */

namespace App\HttpController\Server;

use App\Utils\Code;

class Goodscollect extends Server
{
	/**
	 * 收藏
	 * @method POST
	 * @param  int $goods_id
	 */
	public function add()
	{
		if( $this->verifyResourceRequest() !== true ){
			$this->send( Code::user_access_token_error );
		} else{
			if( $this->validator( $this->post, 'Server/GoodsCollect.add' ) !== true ){
				$this->send( Code::param_error, [], $this->getValidator()->getError() );
			} else{
				$user = $this->getRequestUser();
				\App\Model\GoodsCollect::init()->delGoodsCollect( ['user_id' => $user['id'], 'goods_id' => $this->post['goods_id']] );
				$goods_id = \ezswoole\Db::name( 'Goods' )->where( ['id' => $this->post['goods_id']] )->value( 'id' );
				if( $goods_id ){
					\App\Model\GoodsCollect::init()->addGoodsCollect( [
						'user_id'  => $user['id'],
						'goods_id' => $this->post['goods_id'],
					] );
					$this->send( Code::success );
				} else{
					$this->send( Code::error );
				}
			}
		}
	}

	/**
	 * 取消收藏
	 * @method POST
	 * @param int $goods_id
	 */
	public function del()
	{
		if( $this->verifyResourceRequest() !== true ){
			$this->send( Code::user_access_token_error );
		} else{
			if( $this->validator( $this->post, 'Server/GoodsCollect.del' ) !== true ){
				$this->send( Code::param_error, [], $this->getValidator()->getError() );
			} else{
				$user = $this->getRequestUser();
				\App\Model\GoodsCollect::init()->delGoodsCollect( ['user_id' => $user['id'], 'goods_id' => $this->post['goods_id']] );
				$this->send( Code::success );
			}
		}
	}

	/**
	 * 收藏的状态
	 * @method GET
	 * @param int $goods_id
	 */
	public function state()
	{
		if( $this->verifyResourceRequest() !== true ){
			$this->send( Code::user_access_token_error );
		} else{
			if( $this->validator( $this->post, 'Server/GoodsCollect.state' ) !== true ){
				$this->send( Code::param_error, [], $this->getValidator()->getError() );
			} else{
				$user = $this->getRequestUser();
				$info = \App\Model\GoodsCollect::init()->getGoodsCollectInfo( ['user_id' => $user['id']], ['goods_id' => $this->post['goods_id']] );
				$this->send( Code::success, ['state' => $info ? 1 : 0] );
			}
		}
	}

	/**
	 * 我的商品收藏
	 * @method GET
	 */
	public function mine()
	{
		if( $this->verifyResourceRequest() !== true ){
			$this->send( Code::user_access_token_error );
		} else{
			$user         = $this->getRequestUser();
			$in_goods_ids = \App\Model\GoodsCollect::order( 'create_time desc' )->where( [
				'user_id' => $user['id'],
			] )->column( 'goods_id' );

			if( $in_goods_ids ){
				// TODO
				$goods_search_logic = new \App\Logic\GoodsSearch( ['ids' => $in_goods_ids] );
				$list               = $goods_search_logic->list();
				$count              = $goods_search_logic->count();
				$this->send( Code::success, ['list' => $list, 'total_number' => $count] );
			} else{
				$this->send( Code::success, ['list' => [], 'total_number' => 0] );
			}
		}
	}
}