<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\HttpFoundation\Request;

class AppController extends Controller
{
    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        return $this->render('index.html.twig');
    }

    /**
     * @Route("/patologies", name="patologies")
     */
    public function patologies()
    {
        $patologies = Yaml::parseFile(__DIR__.'/../Resources/patologies.yaml');

        return $this->render('pages/patologies.html.twig', [
            'patologies' => $patologies,
        ]);
    }

    public function patologiesSlider()
    {
        $patologies = Yaml::parseFile(__DIR__.'/../Resources/patologies.yaml');

        return $this->render('pages/patologies_slider.html.twig', [
            'patologies' => $patologies,
        ]);
    }

    /**
     * @Route("/equip", name="equip")
     */
    public function equip()
    {
        return $this->render('pages/equip.html.twig');
    }

    /**
     * @Route("/contact", name="contact")
     */
    public function contact(Request $request, \Swift_Mailer $mailer)
    {
        if ($request->getMethod() == 'POST') {
            $name = $request->get('_name');
            $email = $request->get('_email');
            $phone = $request->get('_phone');
            $message = $request->get('_message');

            if (trim($email) == '') {
                $this->addFlash('error', 'Falta indicar un correu electrònic');

                return $this->redirectToRoute('index');
            }

            if (trim($message) == '') {
                $this->addFlash('error', 'Falta indicar un missatge');

                return $this->redirectToRoute('index');
            }

            $mail = (new \Swift_Message('Formulari de contacte web'))
                ->setFrom('no-reply@psimuva.com')
                ->setTo('info@psimuva.com')
                ->setBody(
                    $this->renderView('emails/contact.html.twig', [
                        'name' => $name,
                        'email' => $email,
                        'phone' => $phone,
                        'message' => $message,
                    ]),
                    'text/html'
                );

            $mailer->send($mail);
        }

        return $this->redirectToRoute('index');
    }
}