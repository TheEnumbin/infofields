<?php
/**
 * 2017-2022 PrestaShop
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
 * @author    MBE Worldwide
 * @copyright 2017-2023 MBE Worldwide
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registered Trademark & Property of MBE Worldwide
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use PrestaShopBundle\Form\Admin\Type\FormattedTextareaType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\CleanHtml;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\DefaultLanguage;
use PrestaShop\PrestaShop\Core\Domain\Category\SeoSettings;
use PrestaShop\PrestaShop\Core\Feature\FeatureInterface;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use PrestaShopBundle\Form\Admin\Type\Material\MaterialChoiceTableType;
use PrestaShopBundle\Form\Admin\Type\ShopChoiceTreeType;
use PrestaShopBundle\Form\Admin\Type\TextWithRecommendedLengthType;
use PrestaShopBundle\Form\Admin\Type\TranslateType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Contracts\Translation\TranslatorInterface;

class InfofieldBuilder
{
    private $fields;
    private $metas;

    public function __construct($form_for, $item_id)
    {
        $fieldsmodel = new FieldsModel();
        $this->fields = $fieldsmodel->get_infofield_by_parent_item($form_for);
        $metamodel = new MetaModel();
        $this->metas = $metamodel->get_meta_by_parent($item_id, $this->fields, null, true);
    }

    public function get_fields()
    {
        return $this->fields;
    }

    public function get_metas()
    {
        return $this->metas;
    }

    public function inf_build_form(FormBuilderInterface $formBuilder, $fields, $metas)
    {
        $inf_ids = [];
        foreach ($fields as $field) {
            $field_params = $this->inf_get_field_params($field);
            $inf_ids[] = $field['id_infofields'];
            $data = $this->inf_prepare_data($metas[$field['id_infofields']], $field['field_type']);
            $field_params['params']['data'] = $data;
            if($field_params['has_translator']) {
                $formBuilder
                ->add(
                    'inf_metafield_' . $field['id_infofields'],
                    TranslatableType::class,
                    $field_params['params'],
                );
            } else {
                $formBuilder
                ->add(
                    'inf_metafield_' . $field['id_infofields'],
                    $field_type['classtype'],
                    [
                        'label' => $field['field_name'],
                        'required' => false,
                        'format' => 'yyyy-M-dd',
                        'input' => 'string',
                        'data' => $data
                    ]
                );
            }
        }
        $inf_ids = array_unique($inf_ids);
        $inf_ids = implode(",", $inf_ids);
        $formBuilder
        ->add(
            'inf_infofield_ids',
            HiddenType::class,
            [
                'data' => $inf_ids,
            ]
        );
    }

    public function inf_get_field_params($field)
    {
        $return_arr = null;
        switch($field['field_type']) {
            case 1:
                $return_arr['params'] = [
                    'type' => TextType::class,
                    'required' => false,
                    'label' => $field['field_name'],
                ];
                $return_arr['has_translator'] = true;
                break;
            case 2:
                $return_arr['params'] = [
                    'type' => FormattedTextareaType::class,
                    'required' => false,
                    'label' => $field['field_name'],
                ];
                $return_arr['has_translator'] = true;
                break;
            case 3:
                $return_arr['params'] = [
                    'type' => TextareaType::class,
                    'required' => false,
                    'label' => $field['field_name'],
                ];
                $return_arr['has_translator'] = true;
                break;
            case 4:
                $return_arr['classtype'] = SwitchType::class;
                $return_arr['has_translator'] = false;
                break;
            case 7:
                $return_arr['classtype'] = DateType::class;
                $return_arr['has_translator'] = false;
                break;
        }

        return $return_arr;
    }

    public function inf_prepare_data($data, $field_type)
    {
        switch($field_type) {
            case 1:
            case 2:
            case 3:
                return $data;
                break;
            case 4:
                $data = array_pop($data);
                return $data;
                break;
            case 7:
                $data = array_pop($data);
                $data = implode('-', json_decode($data, true));
                return $data;
                break;
        }
    }
}
