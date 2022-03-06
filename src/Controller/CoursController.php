<?php

namespace App\Controller;
use App\Entity\Cours;
use App\Entity\Salle;
use App\Form\CoursFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
class CoursController extends AbstractController
{
    /**
     * @Route("/dashboard/cours", name="cours")
     */
    public function index(): Response
    {
        $cours = $this->getDoctrine()->getRepository(cours::class)->findAll();
        return $this->render('cours/index.html.twig', [
            'controller_name' => 'CoursController',
            "cours" => $cours,
        ]);
    }

    /**
     * @Route("/dashboard/addCours", name="addCours")
     */
    public function addcours(Request $request): Response
    {
        $cours = new cours();
        $form = $this->createForm(CoursFormType::class,$cours);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('image')->getData();
            // this condition is needed because the 'image' field is not required

            if ($imageFile) {
                // generate new name to the file image with the function generateUniqueFileName
                $fileName = $this->generateUniqueFileName().'.'.$imageFile->guessExtension();

                // moves the file to the directory where products are stored
                $imageFile->move(
                    $this->getParameter('imagesCour_directory'),
                    $fileName
                );

                // updates the 'product' property to store the image file name
                // instead of its contents
                $cours->setImage($fileName);
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($cours);
            $entityManager->flush();
            return $this->redirectToRoute("cours");


        }
        return $this->render("cours/ajouter.html.twig", [
            "form_title" => "Ajouter un cours",
            "form_cours" => $form->createView(),
        ]);
    }
    /**
     * @Route("/dashboard/modifyCours/{id}", name="modifyCours")
     */
    public function modifyCours(Request $request, int $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $cours = $entityManager->getRepository(cours::class)->find($id);
        $form = $this->createForm(CoursFormType::class, $cours);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $entityManager->flush();
            $this->addFlash('success' , 'L"action a été effectué');
            return $this->redirectToRoute("cours");
        }

        return $this->render("cours/modifier.html.twig", [
            "form_title" => "Modifier un cours",
            "form_cours" => $form->createView(),
        ]);
    }
    /**
     * @Route("/dashboard/deleteCours/{id}", name="deleteCours")
     */
    public function deleteCours(int $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $cours = $entityManager->getRepository(cours::class)->find($id);
        $entityManager->remove($cours);
        $entityManager->flush();
        $this->addFlash('success' , 'L"action a été effectué');


        return $this->redirectToRoute("cours");
    }
    /**
     * @Route("/detail_cours/{id}", name="detailcours")
     */
    public function detailCours(Request $req, $id) {
        $em= $this->getDoctrine()->getManager();
        $cours = $em->getRepository(Cours::class)->find($id);


        return $this->render('cours/DetailCours.html.twig',array(

            'id'=>$cours->getId(),
            'Nom'=>$cours->getNom(),
            'Description'=>$cours->getDescription(),
            'salleassocie'=>$cours->getSalleassocie(),
            'heureD'=>$cours->getHeureD(),
            'heureF'=>$cours->getHeureF(),
            'jour'=>$cours->getJour(),
            'nombre'=>$cours->getNombre(),
             'image'=>$cours->getImage()
        ));


    }


 /**
     * @Route("/detail_coursFront/{id}", name="detailcoursFront")
     */
    public function detailCoursFront(Request $req, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $cours = $em->getRepository(Cours::class)->find($id);
        return $this->render('cours/CoursDetailFront.html.twig', array(

            'id' => $cours->getId(),
            'Nom' => $cours->getNom(),
            'nomCoach' => $cours->getNomCoach(),
            'Description' => $cours->getDescription(),
            'salleassocie' => $cours->getSalleassocie(),
            'heureD' => $cours->getHeureD(),
            'heureF' => $cours->getHeureF(),
            'jour' => $cours->getJour(),
            'nombre' => $cours->getNombre(),
            'image' => $cours->getImage()
        ));

    }


    // fonction qui generer un identifiant unique pour chaque image
    /**
     * @return string
     */
    private function generateUniqueFileName()
    {
        // md5() reduces the similarity of the file names generated by
        // uniqid(), which is based on timestamps
        return md5(uniqid());
    }
    // fonction qui generer un identifiant unique pour chaque image

    /**
     * @Route("/ImprimerPDF/{id}", name="ImprimerPDF")
     */
    public function ImprimerPDF(int $id)
    {
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $pdfOptions->setIsRemoteEnabled(true);
        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);
        // Retrieve the HTML generated in our twig file

        $entityManager = $this->getDoctrine()->getManager();
        $salle = $entityManager->getRepository(Salle::class)->find($id);



        $html = $this->renderView('cours/pdf.html.twig', [
            'title' => "Welcome to our PDF Test",
            "salle" => $salle,

        ]);


        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (force download)
        $dompdf->stream("PDFPlanning.pdf", [
            "Attachment" => true
        ]);
    }


    /**
     * @Route("/ImprimerEXCEL/{id}", name="ImprimerEXCEL")
     */
    public function ImprimerEXCEL(int $id)
    {
$spreadsheet = new Spreadsheet();

    /* @var $sheet \PhpOffice\PhpSpreadsheet\Writer\Xlsx\Worksheet */

        $em= $this->getDoctrine()->getManager();
        $cours = $em->getRepository(Cours::class)->createQueryBuilder('s')
            ->Where(':id MEMBER OF s.salleassocie')
            ->setParameter('id',$id);

$sheet = $spreadsheet->getActiveSheet();

$sheet->setCellValue('A1', ' Formule');
$sheet->setCellValue('A2', 'P/T² ');
$sheet->setCellValue('A3', 'm.h-²');
$sheet->setCellValue('B1', ' P');
$sheet->setCellValue('B2', 'Masse');
$sheet->setCellValue('C1', 'T');
$sheet->setCellValue('C2', 'Stature');
$sheet->setCellValue('C2', 'Stature');
        $sheet->setCellValue('A1', ' Formule');
        $sheet->setCellValue('A2', 'P/T² ');
        $sheet->setCellValue('A3', 'm.h-²');
        $sheet->setCellValue('B1', ' P');
        $sheet->setCellValue('B2', 'Masse');
        $sheet->setCellValue('C1', 'T');
        $sheet->setCellValue('C2', 'Stature');
        $sheet->setCellValue('C2', 'Stature');
        $sheet->setCellValue('A1', ' Formule');
        $sheet->setCellValue('A2', 'P/T² ');
        $sheet->setCellValue('A3', 'm.h-²');
        $sheet->setCellValue('B1', ' P');
        $sheet->setCellValue('B2', 'Masse');
        $sheet->setCellValue('C1', 'T');
        $sheet->setCellValue('C2', 'Stature');
        $sheet->setCellValue('C2', 'Stature');
$sheet->setTitle("Informations IMC");

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


