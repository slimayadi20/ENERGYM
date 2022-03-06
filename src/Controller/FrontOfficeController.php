<?php

namespace App\Controller;

use http\Env\Request;
use HttpRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;
class FrontOfficeController extends AbstractController
{
    /**
     * @Route("/front", name="front_office")
     */
    public function index(\Symfony\Component\HttpFoundation\Request $request): Response
    {
        $response = [];


            $curl = curl_init();

            $w = $request->request->get('w');
            $h = $request->request->get('h');
            $s = $request->request->get('s');
            $a = $request->request->get('a');
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
            $api_result = json_decode($response, true);
            $i='';
            if ($err) {
                echo "cURL Error #:" . $err;
                $i = "error" ;
            } else {

                echo $response;
            }

            return $this->render("front_office/index.html.twig", [
                "response" => $api_result,
            "i"=>$i,
            ]);



    }


}
