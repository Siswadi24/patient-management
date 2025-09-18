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
            $queryParams = [];

            if ($request->has('page')) {
                $queryParams['page'] = $request->get('page');
            }

            if ($request->has('per_page')) {
                $queryParams['per_page'] = $request->get('per_page');
            }

            if ($request->has('search') && !empty($request->get('search'))) {
                $queryParams['search'] = $request->get('search');
            }

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

    public function storePatient(Request $request)
    {
        try {
            $client = new Client();
            $rmNumber = $request->rm_number ?? $this->generateRandomRMNumber();
            $avatarUrl = 'https://api.dicebear.com/6.x/identicon/svg?seed=' . strtolower($request->first_name . '.' . $request->last_name);

            $apiData = [
                'rm_number' => $rmNumber,
                'avatar' => $avatarUrl,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'gender' => $request->gender,
                'birth_place' => $request->birth_place,
                'birth_date' => $request->birth_date,
                'phone_number' => $request->phone_number,
                'street_address' => $request->street_address,
                'city_address' => $request->city_address,
                'state_address' => $request->state_address,
                'emergency_full_name' => $request->emergency_full_name,
                'emergency_phone_number' => $request->emergency_phone_number,
                'identity_number' => $request->identity_number,
                'bpjs_number' => $request->bpjs_number,
                'ethnic' => json_encode(['name' => $request->ethnic]),
                'education' => $request->education,
                'communication_barrier' => $request->communication_barrier,
                'disability_status' => $request->disability_status,
                'married_status' => $request->married_status,
                'father_name' => $request->father_name,
                'mother_name' => $request->mother_name,
                'job' => $request->job,
                'blood_type' => $request->blood_type,
            ];

            $response = $client->request('POST', 'https://mockapi.pkuwsb.id/api/patient', [
                'headers' => [
                    'X-username' => 'admin',
                    'X-password' => 'secret',
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
                'json' => $apiData,
                'timeout' => 30,
            ]);

            $responseData = json_decode($response->getBody()->getContents(), true);

            if ($responseData['success'] ?? false) {
                return redirect()->route('user.patient.records')->with('success', 'Data pasien berhasil ditambahkan dengan No. RM: ' . $rmNumber);
            } else {
                $errorMessage = $responseData['message'] ?? 'Terjadi kesalahan saat menyimpan data';
                $errors = $responseData['errors'] ?? [];

                if (!empty($errors)) {
                    return back()->withErrors($errors)->withInput();
                } else {
                    return back()->withErrors(['api' => $errorMessage])->withInput();
                }
            }
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $responseBody = $e->getResponse()->getBody()->getContents();
                $responseData = json_decode($responseBody, true);

                if ($e->getResponse()->getStatusCode() === 422) {
                    $errorMessage = $responseData['message'] ?? 'Terjadi kesalahan validasi';
                    $errors = $responseData['errors'] ?? [];

                    if (!empty($errors)) {
                        return back()->withErrors($errors)->withInput();
                    } else {
                        return back()->withErrors(['api' => $errorMessage])->withInput();
                    }
                }

                $errorMessage = $responseData['message'] ?? 'Terjadi kesalahan saat menghubungi server';
                return back()->withErrors(['api' => $errorMessage])->withInput();
            }

            return back()->withErrors(['api' => 'Terjadi kesalahan saat menghubungi server. Silakan coba lagi.'])->withInput();
        } catch (\Exception $e) {
            return back()->withErrors(['api' => 'Terjadi kesalahan yang tidak terduga. Silakan coba lagi.'])->withInput();
        }
    }

    public function updatePatient(Request $request, $id)
    {
        try {
            $client = new Client();
            $avatarUrl = null;

            if ($request->hasFile('avatar')) {
                $avatarUrl = 'https://api.dicebear.com/6.x/identicon/svg?seed=' . strtolower($request->first_name . '.' . $request->last_name);
            }

            $apiData = [
                'rm_number' => $request->rm_number,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'gender' => $request->gender,
                'birth_place' => $request->birth_place,
                'birth_date' => $request->birth_date,
                'phone_number' => $request->phone_number,
                'street_address' => $request->street_address,
                'city_address' => $request->city_address,
                'state_address' => $request->state_address,
                'emergency_full_name' => $request->emergency_full_name,
                'emergency_phone_number' => $request->emergency_phone_number,
                'identity_number' => $request->identity_number,
                'bpjs_number' => $request->bpjs_number,
                'ethnic' => json_encode(['name' => $request->ethnic]),
                'education' => $request->education,
                'communication_barrier' => $request->communication_barrier,
                'disability_status' => $request->disability_status,
                'married_status' => $request->married_status,
                'father_name' => $request->father_name,
                'mother_name' => $request->mother_name,
                'job' => $request->job,
                'blood_type' => $request->blood_type,
            ];

            if ($avatarUrl) {
                $apiData['avatar'] = $avatarUrl;
            }

            $response = $client->request('PUT', "https://mockapi.pkuwsb.id/api/patient/{$id}", [
                'headers' => [
                    'X-username' => 'admin',
                    'X-password' => 'secret',
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
                'json' => $apiData,
                'timeout' => 30,
            ]);

            $responseData = json_decode($response->getBody()->getContents(), true);

            if ($responseData['success'] ?? false) {
                return redirect()->route('user.patient.records')->with('success', 'Data pasien berhasil diperbarui');
            } else {
                $errorMessage = $responseData['message'] ?? 'Terjadi kesalahan saat memperbarui data';
                $errors = $responseData['errors'] ?? [];

                if (!empty($errors)) {
                    return back()->withErrors($errors)->withInput();
                } else {
                    return back()->withErrors(['api' => $errorMessage])->withInput();
                }
            }
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $responseBody = $e->getResponse()->getBody()->getContents();
                $responseData = json_decode($responseBody, true);

                if ($e->getResponse()->getStatusCode() === 422) {
                    $errorMessage = $responseData['message'] ?? 'Terjadi kesalahan validasi';
                    $errors = $responseData['errors'] ?? [];

                    if (!empty($errors)) {
                        return back()->withErrors($errors)->withInput();
                    } else {
                        return back()->withErrors(['api' => $errorMessage])->withInput();
                    }
                }

                $errorMessage = $responseData['message'] ?? 'Terjadi kesalahan saat menghubungi server';
                return back()->withErrors(['api' => $errorMessage])->withInput();
            }

            return back()->withErrors(['api' => 'Terjadi kesalahan saat menghubungi server. Silakan coba lagi.'])->withInput();
        } catch (\Exception $e) {
            return back()->withErrors(['api' => 'Terjadi kesalahan yang tidak terduga. Silakan coba lagi.'])->withInput();
        }
    }

    public function deletePatient($id)
    {
        try {
            $client = new Client();
            $response = $client->request('DELETE', "https://mockapi.pkuwsb.id/api/patient/{$id}", [
                'headers' => [
                    'X-username' => 'admin',
                    'X-password' => 'secret',
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
                'timeout' => 30,
            ]);

            $responseData = json_decode($response->getBody()->getContents(), true);

            if ($responseData['success'] ?? false) {
                return back();
            } else {
                $errorMessage = $responseData['message'] ?? 'Terjadi kesalahan saat menghapus data';
                return back()->withErrors(['api' => $errorMessage]);
            }
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $responseBody = $e->getResponse()->getBody()->getContents();
                $responseData = json_decode($responseBody, true);

                $errorMessage = $responseData['message'] ?? 'Terjadi kesalahan saat menghubungi server';
                return back()->withErrors(['api' => $errorMessage]);
            }

            return back()->withErrors(['api' => 'Terjadi kesalahan saat menghubungi server. Silakan coba lagi.']);
        } catch (\Exception $e) {
            return back()->withErrors(['api' => 'Terjadi kesalahan yang tidak terduga. Silakan coba lagi.']);
        }
    }

    private function generateRandomRMNumber(): string
    {
        return str_pad(mt_rand(100000, 999999), 6, '0', STR_PAD_LEFT);
    }

    private function generateUniqueRandomRMNumber(): string
    {
        $maxAttempts = 10;
        $attempt = 0;

        do {
            $rmNumber = str_pad(mt_rand(100000, 999999), 6, '0', STR_PAD_LEFT);
            $attempt++;
            return $rmNumber;
        } while ($attempt < $maxAttempts);

        $timestamp = now()->timestamp;
        return substr((string)$timestamp, -6);
    }
}
