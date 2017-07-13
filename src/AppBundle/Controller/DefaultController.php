<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Adresse;
use AppBundle\Entity\Paiement;
use AppBundle\Entity\Personne;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\ORMException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $defaultData = array('message' => 'Formulaire d\'importation');
        $form = $this->createFormBuilder($defaultData)
            ->add(
                'file',
                FileType::class,
                [
                    'label' => 'Fichier CSV'
                ])
            ->add(
                'submit',
                SubmitType::class,
                [
                    'label' => 'Envoyer'
                ]
            )
            ->getForm();
        $form->handleRequest($request);
        $serializer = new Serializer([new ObjectNormalizer()], [new CsvEncoder()]);
        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form['file']->getData();
            $fileToCsv = $serializer->decode(file_get_contents($file), 'csv');
            $this->storeCSV($fileToCsv);
        }
        $payments = $this->getDoctrine()->getRepository('AppBundle:Paiement')
            ->findAll();

        // replace this example code with whatever you need
        return $this->render('AppBundle:Default:index.html.twig', [
            "fileForm" => $form->createView(),
            "payments" => $payments ?? []
        ]);
    }

    private function storeCSV($fileToCsv)
    {
        $pattern = '/\d{2}\/\d{2}\/\d{4}/';
        foreach ($fileToCsv as $line) {
            foreach ($line as $data) {
                $columns = explode(';', $data);
                $user = $this->getDoctrine()->getRepository('AppBundle:Personne')
                    ->findOneByEmail($columns[2]);
                if (empty($user)) {
                    $user = new Personne();
                    $user
                        ->setNom($columns[0])
                        ->setPrenom($columns[1])
                        ->setEmail($columns[2]);
                }
                if (!empty($columns[3])) {
                    $address = new Adresse();
                    $address
                        ->setAdresse($columns[3])
                        ->setCodePostal($columns[4])
                        ->setVille($columns[5])
                        ->addPersonne($user);
                    $user->setAdresse($address);
                }
                if (preg_match($pattern, $columns[6])) {
                    $payment = new Paiement();
                    $date = $this->convertDate($columns[6]);
                    $payment
                        ->setPaiementDate(new \DateTime($date))
                        ->setPrix($columns[7])
                        ->setPaiementNature($columns[8])
                        ->setPersonne($user);
                    $user->addPaiement($payment);
                } else {
                    $payment = new Paiement();
                    $date = $this->convertDate($columns[5]);
                    $payment
                        ->setPaiementDate(new \DateTime($date))
                        ->setPrix($columns[6])
                        ->setPaiementNature($columns[7])
                        ->setPersonne($user);
                    $user->addPaiement($payment);
                }
                try {
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($user);
                    $em->flush();
                } catch (ORMException $e) {
                } catch (UniqueConstraintViolationException $e) {
                }
            }
        }
    }
    private function convertDate($frDate)
    {
        $parts = explode('/', $frDate);
        return $parts[2].'-'.$parts[1].'-'.$parts[0];
    }

}
