<?php
/*
 * This file is part of the <package> package.
 *
 * (c) Marc Aschmann <maschmann@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Asm\TranslationLoaderBundle\Controller;

use Asm\TranslationLoaderBundle\Entity\Translation;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class TranslationController
 *
 * @package TranslationLoaderBundle\Controller
 * @author Marc Aschmann <maschmann@gmail.com>
 */
class TranslationController extends Controller
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction(Request $request)
    {
        $translations = $this->get('asm_translation_loader.translation_manager')
            ->findAllTranslations();

        if ($request->isXmlHttpRequest()) {
            $response = new JsonResponse(
                array(
                    'translations' => $translations,
                )
            );
        } else {
            $response = $this->render(
                'AsmTranslationLoaderBundle:Translation:list.html.twig',
                array(
                    'translations' => $translations,
                )
            );
        }

        return $response;
    }

    /**
     * @param string $key
     * @param string $locale
     * @param string $domain
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function formAction($key = '', $locale = '', $domain = '')
    {
        $translation = $this->get('asm_translation_loader.translation_manager')
            ->findTranslationBy(
                array(
                    'transKey' => $key,
                    'transLocale' => $locale,
                    'messageDomain' => $domain,
                )
            );

        if (empty($translation)) {
            $translation = new Translation();
        }

        $form = $this->createForm('asm_translation', $translation);

        return $this->render(
            'AsmTranslationLoaderBundle:Translation:form.html.twig',
            array(
                'form' => $form->createView(),
            )
        );
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function createAction(Request $request)
    {
        return new JsonResponse(
            array()
        );
    }

    /**
     * @param string $transKey
     * @param string $transLocale
     * @param string $messageDomain
     * @param Request $request
     * @return JsonResponse
     */
    public function readAction($transKey, $transLocale, $messageDomain, Request $request)
    {
        return new JsonResponse(
            array()
        );
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function updateAction(Request $request)
    {
        $error = array();
        $form = $this->createForm('asm_translation', new Translation());
        $form->handleRequest($request);

        if ($form->isValid()) {
            $status = 200;
            $manager = $this->get('asm_translation_loader.translation_manager');
            /** @var \Asm\TranslationLoaderBundle\Entity\Translation $update */
            $update = $form->getData();
            // get translation from database again to keep date_created
            $translation = $manager->findTranslationBy(
                array(
                    'transKey' => $update->getTransKey(),
                    'transLocale' => $update->getTransLocale(),
                    'messageDomain' => $update->getMessageDomain(),
                )
            );

            $translation
                ->setTransKey($update->getTransKey())
                ->setTransLocale($update->getTransLocale())
                ->setMessageDomain($update->getMessageDomain())
                ->setTranslation($update->getTranslation());

            $manager->updateTranslation($translation);
        } else {
            $status = 403;
            $error = array(
                'error' => $form->getErrors(),
            );
        }

        $result = array_merge(
            array(
                'status' => $status,
            ),
            $error
        );

        return new JsonResponse(
            $result,
            $status
        );
    }

    /**
     * @param string $transKey
     * @param string $transLocale
     * @param string $messageDomain
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteAction($transKey, $transLocale, $messageDomain, Request $request)
    {
        return new JsonResponse(
            array()
        );
    }
}
