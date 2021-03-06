<?php
namespace FacturaScripts\Plugins\PrintTicket\Lib;

use DateTime;
use FacturaScripts\Core\App\AppSettings;
use FacturaScripts\Dinamic\Lib\Ticket\Data\Cashup;
use FacturaScripts\Dinamic\Lib\Ticket\Data\Company;
use FacturaScripts\Plugins\PrintTicket\Lib\Ticket\Template\DefaultCashupTemplate;

class CashupTicket
{
    private $session;
    private $company;

    function __construct($session, $company)
    {
        $this->session = $session;
        $this->company = $company;
    }

    public function getTicket()
    {
        $company = new Company(
            $this->company->nombrecorto,
            $this->company->cifnif,
            $this->company->direccion
        );

        $cashup = new Cashup(
            $this->session->idsesion,
            $this->session->saldoinicial,
            $this->session->saldoesperado,
            $this->session->saldocontado,
            null
        );

        foreach ($this->session->getOperaciones() as $oper) {
            $cashup->addOperation(
                'Operacion: ' . $oper->idoperacion,
                $oper->tipodoc . ' ' . $oper->codigo,
                $oper->total
            );
        }

        $width = AppSettings::get('ticket', 'linelength', 50);
        $template = new DefaultCashupTemplate($width);

        $builder = new Ticket\TicketBuilder($company, $template);
        return $builder->buildFromCashup($cashup);
    }
}
