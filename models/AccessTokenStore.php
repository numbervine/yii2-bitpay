<?php
/**
 * @license Copyright 2017 Thomas Varghese, MIT License
 * see https://github.com/numbervine/yii2-bitpay/blob/master/LICENSE
 */
 namespace numbervine\bitpay\models;

class AccessTokenStore extends \Bitpay\Key
{
  /**
   * @var string
   */
  protected $accessToken = null;

  /**
   * @param string
   * @return AccessTokenStore
   */
  public function setAccessToken($accessToken)
  {
      $this->accessToken = $accessToken;
      return $this;
  }

  /**
   * @return string
   */
  public function getAccessToken()
  {
      return $this->accessToken;
  }

  /**
   * @return string
   */
  public function __toString()
  {
      return (string) $this->accessToken;
  }

  /**
   * @inheritdoc
   */
  public function serialize()
  {
      return serialize(
          array(
              $this->id,
              $this->accessToken
          )
      );
  }

  /**
   * @inheritdoc
   */
  public function unserialize($data)
  {
      list(
          $this->id,
          $this->accessToken
      ) = unserialize($data);
  }

  public function generate(){}

  /**
   * @return boolean
   */
  public function isValid()
  {
    return ($this->accessToken ? true : false);
  }
}
