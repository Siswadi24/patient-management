<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class PatientController extends Controller
{
    public function index(Request $request)
    {
        try {
            $client = new Client();

            // Build query parameters
            $queryParams = [];

            // Pagination parameters
            if ($request->has('page')) {
                $queryParams['page'] = $request->get('page');
            }

            if ($request->has('per_page')) {
                $queryParams['per_page'] = $request->get('per_page');
            }

            // Search parameter
            if ($request->has('search') && !empty($request->get('search'))) {
                $queryParams['search'] = $request->get('search');
            }

            // Individual filter parameters - API supports direct parameter names
            $filterParams = ['gender', 'ethnic', 'education', 'married_status', 'job', 'blood_type'];
            foreach ($filterParams as $param) {
                if ($request->has($param) && !empty($request->get($param))) {
                    $queryParams[$param] = $request->get($param);
                }
            }

            $response = $client->request('GET', 'https://mockapi.pkuwsb.id/api/patient', [
                'headers' => [
                    'X-username' => 'admin',
                    'X-password' => 'secret',
                    'Accept' => 'application/json',
                ],
                'query' => $queryParams,
                'timeout' => 30,
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            $patients = $data['success'] ? $data['data']['data'] : [];
            $pagination = $data['success'] ? [
                'current_page' => $data['data']['current_page'],
                'last_page' => $data['data']['last_page'],
                'per_page' => $data['data']['per_page'],
                'total' => $data['data']['total'],
                'from' => $data['data']['from'],
                'to' => $data['data']['to'],
                'links' => $data['data']['links'],
                'next_page_url' => $data['data']['next_page_url'],
                'prev_page_url' => $data['data']['prev_page_url'],
            ] : [];

        } catch (RequestException $e) {
            $patients = [];
            $pagination = [];
        }

        return Inertia('patient/index', [
            'patients' => $patients,
            'pagination' => $pagination,
            'filters' => [
                'search' => $request->get('search'),
                'gender' => $request->get('gender'),
                'ethnic' => $request->get('ethnic'),
                'education' => $request->get('education'),
                'married_status' => $request->get('married_status'),
                'job' => $request->get('job'),
                'blood_type' => $request->get('blood_type'),
            ]
        ]);
    }
}
