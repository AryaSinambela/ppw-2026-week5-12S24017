<?php

namespace App\Http\Controllers\Soap;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class LibrarySoapController extends Controller
{
    /**
     * GET /soap/sipus.wsdl
     * Menampilkan dokumen kontrak XML WSDL untuk operasi CheckClearanceStatus
     */
    public function wsdl(): Response
    {
        $endpointUrl = url('/soap/sipus');

        $wsdlXml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<definitions name="SipusLibrarySoapService"
             targetNamespace="http://sipus.del.ac.id/soap/clearance"
             xmlns:tns="http://sipus.del.ac.id/soap/clearance"
             xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/"
             xmlns:xsd="http://www.w3.org/2001/XMLSchema"
             xmlns="http://schemas.xmlsoap.org/wsdl/">

    <message name="CheckClearanceStatusRequest">
        <part name="nim_peminjam" type="xsd:string"/>
    </message>

    <message name="CheckClearanceStatusResponse">
        <part name="nim_peminjam" type="xsd:string"/>
        <part name="nama_peminjam" type="xsd:string"/>
        <part name="active_loans" type="xsd:int"/>
        <part name="is_cleared" type="xsd:boolean"/>
        <part name="clearance_note" type="xsd:string"/>
    </message>

    <portType name="SipusLibraryPortType">
        <operation name="CheckClearanceStatus">
            <input message="tns:CheckClearanceStatusRequest"/>
            <output message="tns:CheckClearanceStatusResponse"/>
        </operation>
    </portType>

    <binding name="SipusLibrarySoapBinding" type="tns:SipusLibraryPortType">
        <soap:binding style="rpc" transport="http://schemas.xmlsoap.org/soap/http"/>
        <operation name="CheckClearanceStatus">
            <soap:operation soapAction="http://sipus.del.ac.id/soap/clearance#CheckClearanceStatus"/>
            <input><soap:body use="literal" namespace="http://sipus.del.ac.id/soap/clearance"/></input>
            <output><soap:body use="literal" namespace="http://sipus.del.ac.id/soap/clearance"/></output>
        </operation>
    </binding>

    <service name="SipusLibrarySoapService">
        <port name="SipusLibraryPort" binding="tns:SipusLibrarySoapBinding">
            <soap:address location="{$endpointUrl}"/>
        </port>
    </service>
</definitions>
XML;

        return response($wsdlXml, 200)
            ->header('Content-Type', 'text/xml; charset=utf-8');
    }

    /**
     * POST /soap/sipus
     * Memproses pesan masuk <soap:Envelope> untuk pengecekan Bebas Pustaka Yudisium
     */
    public function handle(Request $request): Response
    {
        $rawXml = trim($request->getContent());

        if (empty($rawXml)) {
            return $this->soapFault('soap:Client', 'Body XML SOAP tidak boleh kosong.');
        }

        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($rawXml);

        if ($xml === false) {
            return $this->soapFault('soap:Client', 'Format dokumen XML cacat (Malformed XML).');
        }

        $nimNodes = $xml->xpath('//*[local-name()="CheckClearanceStatus"]/*[local-name()="nim_peminjam"]')
                 ?: $xml->xpath('//*[local-name()="CheckClearanceStatusRequest"]/*[local-name()="nim_peminjam"]')
                 ?: $xml->xpath('//*[local-name()="nim_peminjam"]');

        if (empty($nimNodes) || trim((string) $nimNodes[0]) === '') {
            return $this->soapFault(
                'soap:Client',
                'Operasi tidak dikenali atau elemen <nim_peminjam> kosong pada body SOAP.'
            );
        }

        $nim = strtoupper(trim((string) $nimNodes[0]));

        $activeLoans = DB::table('peminjamans')
            ->where('nim_peminjam', $nim)
            ->where('status', 'dipinjam')
            ->count();

        $lastRecord = DB::table('peminjamans')
            ->where('nim_peminjam', $nim)
            ->orderByDesc('id')
            ->first();

        $namaPeminjam = $lastRecord->nama_peminjam ?? 'Mahasiswa IT Del';
        $isCleared = $activeLoans === 0 ? 'true' : 'false';
        $note = $activeLoans === 0
            ? 'Memenuhi syarat Bebas Pustaka Yudisium (0 tanggungan buku aktif).'
            : "Belum memenuhi syarat Bebas Pustaka. Masih terdapat {$activeLoans} buku berstatus dipinjam.";

        $safeNim  = htmlspecialchars($nim, ENT_XML1, 'UTF-8');
        $safeNama = htmlspecialchars((string) $namaPeminjam, ENT_XML1, 'UTF-8');
        $safeNote = htmlspecialchars($note, ENT_XML1, 'UTF-8');

        $responseXml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/"
               xmlns:m="http://sipus.del.ac.id/soap/clearance">
    <soap:Header>
        <m:ProviderInfo>SIPUS-Del Legacy SOAP Clearance Engine v1.0</m:ProviderInfo>
    </soap:Header>
    <soap:Body>
        <m:CheckClearanceStatusResponse>
            <m:nim_peminjam>{$safeNim}</m:nim_peminjam>
            <m:nama_peminjam>{$safeNama}</m:nama_peminjam>
            <m:active_loans>{$activeLoans}</m:active_loans>
            <m:is_cleared>{$isCleared}</m:is_cleared>
            <m:clearance_note>{$safeNote}</m:clearance_note>
        </m:CheckClearanceStatusResponse>
    </soap:Body>
</soap:Envelope>
XML;

        return response($responseXml, 200)
            ->header('Content-Type', 'text/xml; charset=utf-8');
    }

    private function soapFault(string $faultCode, string $faultMessage): Response
    {
        $safeMsg = htmlspecialchars($faultMessage, ENT_XML1, 'UTF-8');

        $faultXml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
    <soap:Body>
        <soap:Fault>
            <faultcode>{$faultCode}</faultcode>
            <faultstring>{$safeMsg}</faultstring>
        </soap:Fault>
    </soap:Body>
</soap:Envelope>
XML;

        return response($faultXml, 500)
            ->header('Content-Type', 'text/xml; charset=utf-8');
    }
}