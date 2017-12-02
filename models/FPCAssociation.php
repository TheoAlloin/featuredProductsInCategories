<?php
/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2017 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

/**
 * Association d'un produit à une catégorie
 */
class FPCAssociation extends ObjectModelCore
{
    public $id_association; // Identifiant unique
    public $id_product; // Id du produit associé
    public $id_category; // Id de la catégorie associée
    
    // Structure de la table
    public static $definition = array('table' => 'fpc_association', 'primary' => 'id_association',
        'multilang' => false,
        'fields' => array(
            'id_product' => array('type' => self::TYPE_INT, 'required' => true),
            'id_category' => array('type' => self::TYPE_INT, 'required' => true),
    ));

    /**
     * Constructeur de l'objet
     */
    public function __construct($id_association = null)
    {
        parent::__construct($id_association);
    }

    /**
     * Ajout d'associations entre un produit et une ou plusieurs catégories
     * @id_product = int
     * @id_categories = array()
     */
    public static function addAssociations($id_product, $id_categories)
    {
        foreach ($id_categories as $id_category) {
            $association = new self();
            $association->id_product = $id_product;
            $association->id_category = $id_category;
            $association->save();
        }
    }
    
    /* render filtered categories */
    public static function getCustomIDCategories($id_lang = false, $active = true, $order = true, $where = '')
    {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
            SELECT c.id_category
			FROM `'._DB_PREFIX_.'category` c
			'.Shop::addSqlAssociation('category', 'c').'
			LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON c.`id_category` = cl.`id_category`'.Shop::addSqlRestrictionOnLang('cl').'
			WHERE 1 '.$where.' '.($id_lang ? 'AND `id_lang` = '.(int)$id_lang : '').'
			'.($active ? 'AND `active` = 1' : '' )
        );
        return $result;
    }
    /**
     * Récupération de la liste des éléments
     */
    public static function getList($params = array())
    {
        $sql = new DbQuery();
        $sql->select('*');
        $sql->from('fpc_association');

        if (isset($params ['id_product'])) {
            $sql->where("id_product = " . $params ['id_product']);
        }
        if (isset($params ['id_category'])) {
            $sql->where("id_category = " . $params ['id_category']);
        }

        if (isset($params ['id_product']) && isset($params ['id_category'])) {
            return Db::getInstance()->getRow($sql);
        } else {
            return array_reverse(Db::getInstance()->ExecuteS($sql));
        }
    }

    /**
     * Supression d'un élément
     */
    public static function deleteAssociation($id_product, $id_category)
    {
        $association = self::getList(array('id_product' => $id_product, 'id_category' => $id_category));

        $associationObj = new self($association['id_association']);

        return $associationObj->delete();
    }

    /**
     * Supression de toutes les associations liées à un id_product
     */
    public static function deleteAllAssociationsByProductId($id_product)
    {
        $associations = self::getList(array('id_product' => $id_product));

        if (empty($associations)) {
            return true;
        }

        foreach ($associations as $association) {
            self::deleteAssociation($id_product, $association['id_category']);
        }

        return true;
    }

    /**
     * Supression de toutes les associations liées à un id_product
     */
    public static function deleteAllAssociationsByCategoryId($id_category)
    {
        $associations = self::getList(array('id_category' => $id_category));

        if (empty($associations)) {
            return true;
        }

        foreach ($associations as $association) {
            self::deleteAssociation($association['id_product'], $id_category);
        }

        return true;
    }

    /**
     * Ajout d'un nouvel élément
     */
    public function save()
    {
        $association = self::getList(array('id_product' => $this->id_product, 'id_category' => $this->id_category)); // On vérifie s'il n'éxiste pas déjà une association similaire

        if (empty($association)) {
            parent::save(); // Si aucune association, on sauvegarde
        } else {
            return true; // Sinon on retourne vrai car elle éxiste déjà
        }
    }
}