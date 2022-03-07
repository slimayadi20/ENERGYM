<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use App\Entity\Panier;
use App\Entity\Commande;
use App\Entity\Livraison;
use App\Entity\Promo;
use App\Entity\User;
use App\Entity\Produit;
use App\Repository\UserRepository;
use App\Repository\PromoRepository;
use App\Repository\PanierRepository;
use App\Repository\ProduitRepository;

class FrontOfficeController extends AbstractController
{
    /**
     * @Route("/front", name="front_office")
     */
    public function index(\Symfony\Component\HttpFoundation\Request  $request ,SessionInterface $session, ProduitRepository $produitRepository): Response
    {
        $curl = curl_init();
        $w = $request->request->get('w');
        $h = $request->request->get('h');
        $s = $request->request->get('s');
        $a = $request->request->get('a');
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => "https://bmi.p.rapidapi.com/",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "{\r
    \"weight\": {\r
        \"value\": \"$w\",\r
        \"unit\": \"kg\"\r
    },\r
    \"height\": {\r
        \"value\": \"$h\",\r
        \"unit\": \"cm\"\r
    },\r
    \"sex\": \"m\",\r
    \"age\": \"$a\",\r
    \"waist\": \"34.00\",\r
    \"hip\": \"40.00\"\r
}",
            CURLOPT_HTTPHEADER => [
                "content-type: application/json",
                "x-rapidapi-host: bmi.p.rapidapi.com",
                "x-rapidapi-key: 816440e099mshe5f23d7b49be543p1e825djsn1555b2cea2e8"
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);
        $api_result1 = json_decode($response, true);
// ***********************************************************************************
            return $this->render('front_office/index.html.twig', [
                'controller_name' => 'FrontOfficeController',
                "response" => $api_result1,

            ]);

    }


    /**
     * @Route("/temp", name="temp")
     */
    public function temp(SessionInterface $session, ProduitRepository $produitRepository,Request $request): Response
    {

            return $this->render('front_office/navbar.html.twig');

        }
    /**
     * @Route("/ImprimerEXCEL", name="ImprimerEXCEL")
     */
    public function ImprimerEXCEL()
    {
        $spreadsheet = new Spreadsheet();

        /* @var $sheet \PhpOffice\PhpSpreadsheet\Writer\Xlsx\Worksheet */

        $em= $this->getDoctrine()->getManager();
        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $sheet = $spreadsheet->getActiveSheet();

        $drawing->setPath('uploads/photobmi.png'); // put your path and image here
        $drawing->setCoordinates('B1');
        $drawing->setOffsetX(110);

        $drawing->getShadow()->setDirection(45);
        $drawing->setWorksheet($spreadsheet->getActiveSheet());
        $sheet->setTitle("Tableau IMC");

        // Create your Office 2007 Excel (XLSX Format)
        $writer = new Xlsx($spreadsheet);

        // Create a Temporary file in the system
        $fileName = 'Informations IMC.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);

        // Create the excel file in the tmp directory of the system
        $writer->save($temp_file);

        // Return the excel file as an attachment
        return $this->file($temp_file, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);
    }



}
