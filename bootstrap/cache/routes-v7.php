<?php

app('router')->setCompiledRoutes(
    array (
  'compiled' => 
  array (
    0 => false,
    1 => 
    array (
      '/oauth/token' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'passport.token',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/oauth/authorize' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'passport.authorizations.authorize',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'passport.authorizations.approve',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        2 => 
        array (
          0 => 
          array (
            '_route' => 'passport.authorizations.deny',
          ),
          1 => NULL,
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/oauth/token/refresh' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'passport.token.refresh',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/oauth/tokens' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'passport.tokens.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/oauth/clients' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'passport.clients.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'passport.clients.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/oauth/scopes' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'passport.scopes.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/oauth/personal-access-tokens' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'passport.personal.tokens.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'passport.personal.tokens.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/sanctum/csrf-cookie' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'sanctum.csrf-cookie',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/_ignition/health-check' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'ignition.healthCheck',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/_ignition/execute-solution' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'ignition.executeSolution',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/_ignition/update-config' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'ignition.updateConfig',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/login' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::D0o6ilovrADNfbuN',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/clear-cache' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::crewhN8bn5AjFpDi',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/exception' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'exception',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'POST' => 1,
            'HEAD' => 2,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/thankyou' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'thankyou',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/agent' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'agent.login',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/agent/login' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::OYAQcbEm1mfKuFyr',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'generated::mP9k6XgBWsOBn7lo',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/agent/logout' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'agent.logout',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/agent/dashboard' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'agent.dashboard',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/agent/clients' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'agent.clients.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/agent/clients/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'agent.clients.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/agent/clients/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'agent.clients.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/agent/clients/edit' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::zvZkVQx5kyjymI2c',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/agent/clients/get-recipients' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'agent.clients.getrecipients',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/agent/clients/get-allclients' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'agent.clients.getallclients',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/agent/get-templates' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'agent.clients.gettemplates',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/agent/sendmail' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'agent.clients.sendmail',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/agent/create-note' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'agent.clients.createnote',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/agent/getnotedetail' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'agent.clients.getnotedetail',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/agent/deletenote' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'agent.clients.deletenote',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/agent/prospects' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'agent.clients.prospects',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/agent/viewnotedetail' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::TdRTRDT81N4iG7lj',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/agent/viewapplicationnote' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::MCdZIiopbUT2VpuY',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/agent/archived' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'agent.clients.archived',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/agent/change-client-status' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'agent.clients.updateclientstatus',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/agent/get-activities' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'agent.clients.activities',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/agent/get-application-lists' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'agent.clients.getapplicationlists',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/agent/saveapplication' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'agent.clients.saveapplication',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/agent/get-notes' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'agent.clients.getnotes',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/agent/convertapplication' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'agent.clients.convertapplication',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/agent/deleteservices' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'agent.clients.deleteservices',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/agent/upload-document' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'agent.clients.uploaddocument',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/agent/deletedocs' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'agent.clients.deletedocs',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/agent/renamedoc' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'agent.clients.renamedoc',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/agent/savetoapplication' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::TqJRFRCxciNUCVcc',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/agent/interested-service' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::Nu7xjP6fFyzxElRe',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/agent/edit-interested-service' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::5oSxS8hLHvvY9QmM',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/agent/showproductfeeserv' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::fTQ7MXyz4LQu0bxN',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/agent/servicesavefee' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::LROOj8Nqlxj7jfQ0',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/agent/pinnote' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::YkWMsw08Bjh8T3Je',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/agent/getpartner' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::mmoWZkzSosQPSCjx',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/agent/getproduct' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'agent.quotations.getproduct',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/agent/getbranch' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'agent.quotations.getbranch',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/agent/get-services' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::ChXupgBewSAzkHnY',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/agent/getintrestedservice' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::oo7Be2X64X09gp3l',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/agent/getintrestedserviceedit' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::i8VlH776kOjTkg92',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/agent/getapplicationdetail' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::m7hKhqpWdtUUWGnD',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/agent/getapplicationnotes' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::cgRdPhPXMx4yGUJZ',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.login',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/login' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::ezHZGaVFO21tScag',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'generated::1UQ5grWzRyDdyU6A',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/logout' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.logout',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/dashboard' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.dashboard',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/get_customer_detail' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.get_customer_detail',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/my_profile' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.my_profile',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'generated::GyzFz4PQdPNaWgoF',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/change_password' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.change_password',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'generated::Fvc9Ly3CWaXPqZ2t',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/sessions' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.sessions',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'generated::qFqvUORqw2GV82JK',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/update_action' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::8rSKLhJTPI0cPaRd',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/approved_action' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::D75FR1NAjq6iBEep',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/process_action' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::YwFpWpLcPVm7uT4U',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/archive_action' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::sJhH3WMOco1EznC4',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/declined_action' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::knHc5Rmmp9Dpukk2',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/delete_action' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::FCNt9DP2Ov4yjN5O',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/delete_slot_action' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::KKaTvuXeOK5gvlhA',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/move_action' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::Iu9e10Z6m28P1JOA',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/add_ckeditior_image' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'add_ckeditior_image',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/get_chapters' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.get_chapters',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/website_setting' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.website_setting',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'generated::PHLZY8xjuT7VksOm',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/get_states' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::aml4frH1gXJIy4tL',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/settings/taxes/returnsetting' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.returnsetting',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/settings/taxes/taxrates' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.taxrates',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/settings/taxes/taxrates/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.taxrates.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/settings/taxes/taxrates/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.taxrates.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/settings/taxes/taxrates/edit' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::rfn0i0phOQ9YvlvT',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/settings/taxes/savereturnsetting' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.savereturnsetting',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/getsubcategories' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::hvSNnX3P0JXlaerQ',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/getproductbranch' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::CdB4f1WXnyyHFBPY',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/getservicemodal' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::FwIHdXO1IWKX9k1V',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/getassigneeajax' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::nIsiohHbqGqK89GW',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/getpartnerajax' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::wnrHf5QiGJZcKwqP',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/checkclientexist' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::k0GQEvxgxPfCDRTd',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/uploadfile/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.media.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/uploadfile/index' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.media.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/uploadfile/delete' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.media.delete',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/users' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.users.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/users/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.users.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/users/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.users.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/users/edit' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::vPSD2AobEmCOmHzS',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/users/savezone' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::pYV7Jmjt8IVPAV9P',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/users/active' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.users.active',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/users/inactive' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.users.inactive',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/users/invited' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.users.invited',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/staff' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.staff.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/staff/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.staff.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/staff/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.staff.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/staff/edit' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::7WDIKyDbYnahlUyB',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/customer' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.customer.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/customer/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.customer.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/customer/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.customer.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/customer/edit' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::qq2lJthJVLy7SLCP',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/customer/upload' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.customer.upload',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/users/clientlist' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.users.clientlist',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/users/createclient' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.users.createclient',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/users/storeclient' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.users.storeclient',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/users/editclient' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::U9MCObypmPjbd9rM',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/followup/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.followup.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/followup/list' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.followup.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/followup/compose' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.followup.compose',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/usertype' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.usertype.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/usertype/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.usertype.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/usertype/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.usertype.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/usertype/edit' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::92Vsb1UtF8oWWIF0',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/userrole' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.userrole.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/userrole/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.userrole.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/userrole/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.userrole.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/userrole/edit' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::POKTNEpSh2o15Jt9',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/services' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.services.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/services/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.services.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/services/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.services.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/services/edit' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::oXRlhvMbjp4m3Muw',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/contact' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.managecontact.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/contact/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.managecontact.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/managecontact/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.managecontact.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/contact/add' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.managecontact.add',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/contact/edit' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::2Gb74o1weYzXgr5G',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/contact/storeaddress' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::ktatWWEAS8paHsbj',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/leads' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.leads.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/leads/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.leads.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/leads/assign' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.leads.assign',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/leads/edit' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::7vZo7N26eXtDxF1k',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/get-notedetail' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::jeEEcCUymkm8zhdm',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/followup/update' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::J6DIpsMPId6fHvjj',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/leads/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.leads.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/leads/convert' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::m3qiYOGOSrTahWsq',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/invoice/edit' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::XdefMvULNSyuuD2t',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/invoice/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.invoice.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/invoice/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.invoice.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/invoice/detail' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.invoice.detail',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/invoice/email' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::6MJzHlqsuQXvp8ln',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/invoice/editpayment' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.invoice.editpayment',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/invoice/invoicebyid' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.invoice.invoicebyid',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/invoice/history' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.invoice.history',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/invoice/paymentsave' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.invoice.paymentsave',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/invoice/editpaymentsave' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.invoice.editpaymentsave',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/invoice/addcomment' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.invoice.addcomment',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/invoice/sharelink' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.invoice.sharelink',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/invoice/disablelink' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.invoice.disablelink',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/invoice/exportall' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.invoice.exportall',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/invoice/printall' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.invoice.customer_invoice_printall',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/invoice/reminder' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::fMhN7kwtFIOXMXLp',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/invoice/attachfile' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.invoice.attachfile',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/invoice/getattachfile' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.invoice.getattachfile',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/invoice/removeattachfile' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.invoice.removeattachfile',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/invoice/attachfileemail' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.invoice.attachfileemail',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/email_templates' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.email.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/email_templates/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.email.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/email_templates/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.email.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/edit_email_template' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::a4rEame8LbZlfMe8',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/edit_seo' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::NXmkbcDeDpcATWkj',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/api-key' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.edit_api',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'generated::YtsvKq0fvxwm09Qe',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/offer/index' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.offer.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/offer/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.offer.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/offer/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.offer.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/offer/edit' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::R7SuTVVWOdsxe1zb',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/photo-gallery/getlist' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.photo.getlist',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/photo-gallery/uploadlist' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.photo.uploadlist',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/photo-gallery/update_action' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.photo.update_action',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/clients' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/clients/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/clients/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/clients/edit' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::RStQXYyHxBjTvf9n',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/clients/followup/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::aRTYSgOzH39dQbfF',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/clients/followup_application/store_application' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::qJViyjeP7wMpJwUS',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/clients/followup/retagfollowup' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::JuWyJRPHNuCj1cVt',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/clients/removetag' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::s4GQDl3O2nia9ZgQ',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/clients/get-recipients' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.getrecipients',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/clients/get-onlyclientrecipients' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.getonlyclientrecipients',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/clients/get-allclients' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.getallclients',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/clients/change_assignee' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::wFmkQAjm6CNddyxq',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/get-templates' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.gettemplates',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/sendmail' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.sendmail',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/create-note' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.createnote',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/getnotedetail' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.getnotedetail',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/deletenote' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.deletenote',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/deleteactivitylog' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.deleteactivitylog',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/not-picked-call' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.notpickedcall',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/prospects' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.prospects',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/viewnotedetail' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::OIGYprXwIXXMQOpu',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/viewapplicationnote' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::Trkzpj1X6BEOR1RE',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/saveprevvisa' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::b3mUfZXwJRBMW6Pu',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/saveonlineprimaryform' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::TBUHlXm0jsRByEBi',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/saveonlinesecform' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::KMqVkve6zZnZztMz',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/saveonlinechildform' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::GIYUm2POrGwl1Zlq',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/archived' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.archived',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/change-client-status' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.updateclientstatus',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/get-activities' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.activities',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/get-application-lists' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.getapplicationlists',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/saveapplication' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.saveapplication',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/get-notes' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.getnotes',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/convertapplication' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.convertapplication',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/deleteservices' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.deleteservices',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/upload-document' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.uploaddocument',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/deletedocs' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.deletedocs',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/renamedoc' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.renamedoc',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/savetoapplication' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::J13LXUf7hkKcD4cq',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/products' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.products.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/products/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.products.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/products/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.products.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/products/edit' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::9cAydT5dNix71pyA',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/products-import' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.products.import',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/products/get-recipients' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.products.getrecipients',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/products/get-allclients' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.products.getallclients',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/partners' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.partners.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/partners/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.partners.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/partners/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.partners.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/partners/edit' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::CPdwmc3lFz77kZHl',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/getpaymenttype' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.partners.getpaymenttype',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/partners/get-recipients' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.partners.getrecipients',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/partners/get-allclients' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.partners.getallclients',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/branch' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.branch.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/branch/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.branch.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/branch/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.branch.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/branch/edit' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::LozYAjeFZ92evAUP',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/quotations' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.quotations.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/quotations/client' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.quotations.client',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/quotations/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.quotations.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/quotations/edit' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::nOVSkPmiwRoOIXzH',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/quotations/template' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.quotations.template.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/quotations/template/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.quotations.template.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/quotations/template/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.quotations.template.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/quotations/template/edit' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::kHKQIP0GB1SbjsFL',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/quotations/archived' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.quotations.archived',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/quotations/changestatus' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.quotations.changestatus',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/quotations/sendmail' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.quotations.sendmail',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/getpartner' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.quotations.getpartner',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/getpartnerbranch' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.quotations.getpartnerbranch',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/getsubjects' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::cDk00rBtn2IWTxzR',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/getbranchproduct' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.quotations.getbranchproduct',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/getproduct' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.quotations.getproduct',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/getbranch' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.quotations.getbranch',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/getnewPartnerbranch' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.quotations.getnewPartnerbranch',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/agents/active' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.agents.active',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/agents/inactive' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.agents.inactive',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/agents/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.agents.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/agents/import' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.agents.import',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/agents/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.agents.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/agents/savepartner' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::nRZHdCTJP4TWsYZr',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/agents/edit' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::kvsvOelYcXz5owBe',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/agents/import/business' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::U9JPxJ381oKwYmoZ',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/agents/import/individual' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::jnfA8LgQ5HMamcYW',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/tasks' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.tasks.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/tasks/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.tasks.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/tasks/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.tasks.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/tasks/groupstore' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.tasks.groupstore',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/tasks/deletegroup' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.tasks.deletegroup',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/get-tasks' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.tasks.gettasks',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/get-task-detail' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.tasks.gettaskdetail',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/update_task_comment' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::oel8DXgGHLChGHTW',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/update_task_description' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::6NBbutEweCYY69pw',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/update_task_status' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::y9sQp6ACA4NMBfce',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/update_task_priority' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::eQXxNzFQeYsJESyn',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/updateduedate' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::AhGCk6HWAObxkXMG',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/task/change_assignee' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::lzPTnU2HrfdUWyb4',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/invoice/general-invoice' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.invoice.general-invoice',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/interested-service' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::uieRT1SUKU0p5Ayu',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/edit-interested-service' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::bvVzFha8XD24HkPk',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/get-services' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::G8mRs7AK1BypeZPG',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/showproductfeeserv' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::qYcTphXggFQ1XF2V',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/servicesavefee' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::YT6DiktuW3ZZsXB1',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/upload-mail' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::S5MKlVbMAlByqbFM',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/updatefollowupschedule' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::4GhwF0YAXXibY12S',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/pinnote' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::RK2SGkj4HgnsMTbP',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/pinactivitylog' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::kPgBjYmdVFaXcX51',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/getintrestedservice' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::DDxODIO61wwCuOfa',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/application/saleforcastservice' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::obeliaRyI23kyCsZ',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/getintrestedserviceedit' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::EVQLuqhIDSQM8SZD',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/saveeducation' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::YcUjmevnCRXIL1MT',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/editeducation' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::ZmL09JpgGNJiN2ze',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/get-educations' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::EYqXRXxSw5lNboIg',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/getEducationdetail' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::Jh9bWBwrNmJbaaTH',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/delete-education' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::aFdvZWKtExf3oVsy',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/edit-test-scores' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::vn74qRCMTMirW2cV',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/other-test-scores' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::NAT74htPiRbotnJm',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/create-invoice' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::MJzEQRESkwZF6tDn',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/invoice/general-store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::JknB5K1YSliVJZHN',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/invoice/delete-payment' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::9Ty1eDQmisDigQJs',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/invoice/payment-store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::GDgRxVoh8KXKWFzu',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/get-invoices' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::wt1q52YzTilo9y3t',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/get-invoices-pdf' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::C5u1Jdm5NjpUZXDS',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/delete-invoice' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::8K30mCMzLKMp3Jyg',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/invoice/general-edit' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::XmDH1AXXAozOPSko',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/invoice/com-store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::Qj2N3TPXp6uYipIz',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/invoice/paid' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.invoice.paid',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/invoice/unpaid' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.invoice.unpaid',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/invoice' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.invoice.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/getapplicationdetail' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::zX5RFWLEvrU4UHri',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/updatestage' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::ktWQfjnNKajmv5xn',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/completestage' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::ordxr52WsrBZb0cB',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/updatebackstage' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::8bBGSGxaKVXKQ9XG',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/get-applications-logs' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::kEMpBwzz7nUSZ9nJ',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/get-applications' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::EeNLLLNg1a0GZSho',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/create-app-note' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::GrUG5UwtnoDRTNgP',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/getapplicationnotes' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::PYsjLVqcvKp29coG',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/application-sendmail' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::W5SvCLVv17ACcKrF',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/application/updateintake' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::5vLt6oFtTcb6CrIY',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/application/updatedates' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::cjwtnqKtJ3Dfnbeg',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/application/updateexpectwin' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::e0lepIw3Zo2kqN9p',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/application/getapplicationbycid' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::ClPbnQ8xeOnnzrYg',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/application/spagent_application' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::XKPQHg7B7hoIN1p9',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/application/sbagent_application' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::b2JrA6aU8hq1bWYW',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/application/application_ownership' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::vtiHOqG3tsP4zlzs',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/application/saleforcast' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::8s9EO3eKsTaMJ5hT',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/superagent' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::NKwxV3kj79juEgkA',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/subagent' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::aV5PObj7L3CLaemR',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/showproductfee' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::q5rnIRRrqYNYzzlX',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/applicationsavefee' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::SvCTg1RZVSQKuiR4',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/add-checklists' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::RKsDlNawXgueRJKH',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/application/checklistupload' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::cH6q9wHczqZ6zfyS',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/deleteapplicationdocs' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::5MJdgqvyTwgLMl9V',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/application/publishdoc' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::8oSvzn1X3qKxWhsz',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/payment' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.account.payment',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/income-sharing/payables/unpaid' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.account.payableunpaid',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/income-sharing/payables/paid' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.account.payablepaid',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/income-payment-store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::WbFJGqzdTtM71Xzt',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/revert-payment' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::euc0WZ1WMpZJjtOw',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/income-sharing/receivables/unpaid' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.account.receivableunpaid',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/income-sharing/receivables/paid' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.account.receivablepaid',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/group-invoice/unpaid' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.invoice.unpaidgroupinvoice',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/group-invoice/paid' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.invoice.paidgroupinvoice',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/group-invoice/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.invoice.creategroupinvoice',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/invoice-schedules' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.invoice.invoiceschedules',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/paymentschedule' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.invoice.paymentschedule',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/setup-paymentschedule' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::yYswITU8KhaijCzD',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/editpaymentschedule' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.invoice.editpaymentschedule',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/scheduleinvoicedetail' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::PO8svuMJ0feQdLlr',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/addscheduleinvoicedetail' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::kyEvXtBo7TZVt5Xm',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/get-all-paymentschedules' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::0TAX9vDrcR6D0WR4',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/deletepaymentschedule' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::ZbXuAcxefm6HQLp1',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/product-type' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.feature.producttype.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/product-type/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.feature.producttype.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/product-type/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.feature.producttype.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/product-type/edit' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::J3lnDSBdlCkHfMkf',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/profiles' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.feature.profiles.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/profiles/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.feature.profiles.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/profiles/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.feature.profiles.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/profiles/edit' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::Aua2GOugKBxopme5',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/partner-type' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.feature.partnertype.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/partner-type/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.feature.partnertype.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/partner-type/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.feature.partnertype.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/partner-type/edit' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::FocBuRzuXqgdc3FO',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/visa-type' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.feature.visatype.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/visa-type/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.feature.visatype.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/visa-type/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.feature.visatype.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/visa-type/edit' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::T7hePriq50EhlbXH',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/master-category' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.feature.mastercategory.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/master-category/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.feature.mastercategory.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/master-category/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.feature.mastercategory.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/master-category/edit' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::Ooohly5YA0cyyVW5',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/lead-service' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.feature.leadservice.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/lead-service/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.feature.leadservice.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/lead-service/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.feature.leadservice.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/lead-service/edit' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::1N7KRxOix7YIipYp',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/tax' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.feature.tax.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/tax/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.feature.tax.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/tax/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.feature.tax.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/tax/edit' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::KzB1sVjXwoLCbsjW',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/subjectarea' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.feature.subjectarea.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/subjectarea/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.feature.subjectarea.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/subjectarea/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.feature.subjectarea.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/subjectarea/edit' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::TFnksSWa17gTuLCF',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/subject' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.feature.subject.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/subject/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.feature.subject.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/subject/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.feature.subject.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/subject/edit' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::OQalgagts0a7MqPW',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/source' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.feature.source.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/source/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.feature.source.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/source/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.feature.source.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/source/edit' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::wLTMdhOc4cDIq4iq',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/tags' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.feature.tags.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/tags/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.feature.tags.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/tags/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.feature.tags.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/tags/edit' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::dehcoJIXo3xIxTew',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/checklist' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.checklist.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/checklist/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.checklist.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/checklist/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.checklist.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/checklist/edit' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.checklist.update',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/enquirysource' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.enquirysource.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/enquirysource/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.enquirysource.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/enquirysource/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.enquirysource.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/enquirysource/edit' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.enquirysource.update',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/feetype' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.feetype.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/feetype/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.feetype.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/feetype/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.feetype.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/feetype/edit' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.feetype.update',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/workflow' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.workflow.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/workflow/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.workflow.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/workflow/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.workflow.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/workflow/edit' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.workflow.update',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/partner/saveagreement' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::z7ozV5Xs1a1lPY7W',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/partner/create-contact' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::2BYBhqlHnim7ufYP',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/get-contacts' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::OwSUysa9kV8fwXQR',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/deletecontact' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::lQ2dR8SgoyUyfNW3',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/getcontactdetail' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::7OF3kfmcIiYvigPb',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/partners-import' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.partners.import',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/partner/create-branch' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::UHg7yJU6XHfgzCNh',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/get-branches' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::AV0TDJY0Cuc4V7EK',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/getbranchdetail' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::8aupSeTL2yvDsoZe',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/deletebranch' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::TBahM3VYzCZpt3jO',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/saveacademic' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::kgSCdcuBTfUNCrLx',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/saveotherinfo' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::m4QX0N4ajsUDOpBi',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/product/getotherinfo' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::KfvGtDDG8fwB9TtL',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/get-all-fees' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::Ey3XjHHQ94EDRQWe',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/savefee' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::thC3YijPdM5vMJgQ',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/getfeeoptionedit' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::snqDjammUyfK7ow6',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/editfee' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::O0CZr07e8CXrq81d',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/deletefee' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::JuB9KGR5KucePolw',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/partner/addtask' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::CQOL8fyYTaqjhRYR',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/partner/get-tasks' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::LqloRgmpwbwfqX0F',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/partner/get-task-detail' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::4upzuSNDXdoNLWzv',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/partner/savecomment' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::lKpeY8mwkhZ6MHno',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/change-task-status' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::5Oth5nBLL2rRiKRk',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/change-task-priority' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::7gi1ceMQ9m28tVqJ',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/promotion/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::KwK1MBT4qyh4sqGN',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/promotion/edit' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::tydFW5gVLM4mVtBB',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/get-promotions' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::7jz5h15pJ771yGSF',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/getpromotioneditform' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::5JOM0CMM5rCQggpv',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/change-promotion-status' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::KZHuAs3Lg6uRYm3q',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/applications' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.applications.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/applications/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.applications.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/discontinue_application' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::5TYCUaw1gV1PHKJq',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/revert_application' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::qSizMxQV0XWxWVPA',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/applications-import' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.applications.import',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/migration' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.migration.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/office-visits' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.officevisits.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/office-visits/waiting' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.officevisits.waiting',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/office-visits/attending' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.officevisits.attending',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/office-visits/completed' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.officevisits.completed',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/office-visits/archived' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.officevisits.archived',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/office-visits/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.officevisits.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/checkin' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::EfrSnUCYcjHx1bxz',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/get-checkin-detail' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::OPF2f0xwlBXIsCad',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/update_visit_purpose' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::n6fjOBs3DuB8o726',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/update_visit_comment' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::btgCzV5EugnIVR4f',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/attend_session' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::Oje1Hvl6KMVp3oyx',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/complete_session' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::ikDGoQfHv15OYx6w',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/office-visits/change_assignee' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::1H1K7ltV1VlUavcH',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/enquiries' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.enquiries.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/enquiries/archived-enquiries' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.enquiries.archived',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/audit-logs' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.auditlogs.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/report/client' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.reports.client',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/report/application' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.reports.application',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/report/invoice' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.reports.invoice',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/report/office-visit' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.reports.office-visit',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/report/sale-forecast/application' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.reports.saleforecast-application',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/report/sale-forecast/interested-service' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.reports.interested-service',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/report/task/personal-task-report' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.reports.personal-task-report',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/report/task/office-task-report' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.reports.office-task-report',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/reports/visaexpires' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::1I2muTBskR0PP0v8',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/followup-dates' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::SvPsB7k89KP7aJv4',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/reports/agreementexpires' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::uNfb1UY2A3gPULBO',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/report/noofpersonofficevisit' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.reports.noofpersonofficevisit',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/report/clientrandomlyselectmonthly' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.reports.clientrandomlyselectmonthly',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/report/save_random_client_selection' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::uVOARoPalfC34hkg',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/save_tag' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::vR2MBj2mCTXraUGx',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/emails' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.emails.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/emails/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.emails.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/emails/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.emails.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/emails/edit' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.emails.update',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/crm_email_template' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.crmemailtemplate.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/crm_email_template/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.crmemailtemplate.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/crm_email_template/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.crmemailtemplate.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/crm_email_template/edit' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.crmemailtemplate.update',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/gen-settings' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.gensettings.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/gen-settings/update' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.gensettings.update',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/fetch-notification' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::3sPqbPJTCvG4AbcS',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/fetch-messages' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::xDfS8VcyFL7gpjMq',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/upload-checklists' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.upload_checklists.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/upload-checklists/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.upload_checklistsupload',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/teams' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.teams.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/teams/edit' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::jmRvbWTYBF4ZX6xL',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/teams/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.teamsupload',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/all-notifications' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::mrqf3v7fxVlqpEaD',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/assignee' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'assignee.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'assignee.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/assignee/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'assignee.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/assignee-completed' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::csv9iLmYPvdxpj9I',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/update-task-completed' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::jFzc9797ftyP1gmx',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/update-task-not-completed' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::fUX0JdT8caKpjecn',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/assigned_by_me' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'assignee.assigned_by_me',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/assigned_to_me' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'assignee.assigned_to_me',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/activities_completed' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'assignee.activities_completed',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/clients/personalfollowup/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::FXGr65GNeMHXDngm',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/clients/updatefollowup/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::JDfXR0295XfAhEYf',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/clients/reassignfollowup/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::gHER0PQ5vxDhX1Bc',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/clients/update-session-completed' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.updatesessioncompleted',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/fetch-InPersonWaitingCount' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::kW1H1WJN2nVn0TWQ',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/fetch-TotalActivityCount' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::jgLrF3wwzc2M3gnt',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/activities' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'assignee.activities',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/activities/list' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'activities.list',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/get_assignee_list' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::REuJAIVEISHU4iGf',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/is_email_unique' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::Q2BTdBtKJv0fBe4m',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/is_contactno_unique' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::4n8eKivBej6GAhyM',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/merge_records' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'client.merge_records',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/clients/update-email-verified' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::VraGxos9s2sRH8cQ',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/promo-code' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.feature.promocode.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/promo-code/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.feature.promocode.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/promo-code/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.feature.promocode.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/promo-code/edit' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::FKVtLIAweLAOgWCM',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/promo-code/checkpromocode' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::ymXyGi7X8ACYY4IU',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/address_auto_populate' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::9EfAky4mJ6T2EQxQ',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/client/createservicetaken' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::ngWIz5aogww9iQJb',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/client/removeservicetaken' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::aGgCLQ2aCr3xLGhi',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/client/getservicetaken' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::xpKmmN7a7mndwjsc',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/gettagdata' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::OijEhFzG1IbiBoE8',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/clients/saveaccountreport' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.saveaccountreport.update',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/clients/getTopReceiptValInDB' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.getTopReceiptValInDB',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/clients/getClientReceiptInfoById' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.getClientReceiptInfoById',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/clients/clientreceiptlist' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.clientreceiptlist',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/validate_receipt' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'client.validate_receipt',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/commissionreport' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.commissionreport',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/commissionreport/list' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.commissionreportlist',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/application/updateStudentId' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::NTjXNuiIjQgYDVxy',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/showproductfeelatest' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::aaM2bvHI1ZmHWXgk',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/applicationsavefeelatest' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::K6BgF2F1NMwX5jR6',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/documentchecklist' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.feature.documentchecklist.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/documentchecklist/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.feature.documentchecklist.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/documentchecklist/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.feature.documentchecklist.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/documentchecklist/edit' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::B5k3c0EFVU057f5b',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/add-alldocchecklist' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.addalldocchecklist',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/upload-alldocument' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.uploadalldocument',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/notuseddoc' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.notuseddoc',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/renamechecklistdoc' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.renamechecklistdoc',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/verifydoc' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.verifydoc',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/deletealldocs' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.deletealldocs',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/renamealldoc' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.renamealldoc',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/backtodoc' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.backtodoc',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/partners-inactive' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.partners.inactive',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/partner_change_to_inactive' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::7NNqpHSYMRsY46Tn',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/partner_change_to_active' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::9F0IBw5niT4vHjmR',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/partners/savepartnerstudentinvoice' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.partners.savepartnerstudentinvoice.update',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/partners/getTopReceiptValInDB' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.partners.getTopReceiptValInDB',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/partners/getEnrolledStudentList' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.partners.getEnrolledStudentList',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/partners/savepartnerrecordinvoice' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.partners.savepartnerrecordinvoice.update',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/partners/savepartnerrecordpayment' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.partners.savepartnerrecordpayment.update',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/partners/getRecordedInvoiceList' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.partners.getRecordedInvoiceList',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/partners/update-student-status' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.partners.updateStudentStatus',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/partners/getStudentInfo' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.partners.getStudentInfo',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/partners/getStudentCourseInfo' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.partners.getStudentCourseInfo',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/partners/getTopInvoiceValInDB' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.partners.getTopInvoiceValInDB',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/partners/updateInvoiceSentOptionToYes' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.partners.updateInvoiceSentOptionToYes',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/partners/getInfoByInvoiceId' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.partners.getInfoByInvoiceId',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/partners/getEnrolledStudentListInEditMode' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.partners.getEnrolledStudentListInEditMode',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/partners/deleteStudentRecordByInvoiceId' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.partners.deleteStudentRecordByInvoiceId',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/partners/deleteStudentRecordInvoiceByInvoiceId' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.partners.deleteStudentRecordInvoiceByInvoiceId',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/partners/deleteStudentPaymentInvoiceByInvoiceId' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.partners.deleteStudentPaymentInvoiceByInvoiceId',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/upload-partner-fetch-mail' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::NdzqjHEQJ1Ci0BT0',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/upload-partner-sent-fetch-mail' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::CaRGga7SwyQvnuGD',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/applications-overdue' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.applications.overdue',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/applications-finalize' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.applications.finalize',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/partners/followup_partner/store_partner' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::QYxHEiKaXgEgDUeq',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/clients/fetchClientContactNo' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::e2sQNt4Rs99h3OL2',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/partners/fetchPartnerContactNo' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::8f9hlEsTCCLHEeoL',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/partners/update-student-application-overall-status' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.partners.updateStudentApplicationOverallStatus',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/add-student-note' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.partners.addstudentnote',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/get-partner-activities' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.partners.activities',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/update-note-deadline-completed' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::1RSQkGKYGsOTvXcN',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/extenddeadlinedate' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::kw7qYt6QRjXMztif',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/refund_application' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::ltGa8jEHxejPiTrp',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/partners/save-student-note' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.partners.saveStudentNote',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/get-partner-notes' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.partners.getPartnerNotes',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/sendmsg' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.sendmsg',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/verify/is-phone-verify-or-not' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'verify.is-phone-verify-or-not',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/verify/send-code' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'verify.send-code',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/verify/check-code' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'verify.check-code',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/sms' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'sms.form',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'sms.send',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/sms/responses' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'sms.responses',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/is_greview_mail_sent' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.isgreviewmailsent',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/mail/enhance' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.mail.enhance',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/upload-partner-document-upload' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::QoBeQEfJfTiAOaRX',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/download-document' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::rvUlCxkV2jsZqf7C',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/email-verify' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'emailVerify',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/verify-dob' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::QwZoCRiDCJp5hbzr',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/editclient' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'editclient',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/home' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'home',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/broadcasting/auth' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::ilD4VyeVppWtweC4',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'POST' => 1,
            'HEAD' => 2,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
    ),
    2 => 
    array (
      0 => '{^(?|/oauth/(?|tokens/([^/]++)(*:32)|clients/([^/]++)(?|(*:58))|personal\\-access\\-tokens/([^/]++)(*:99))|/a(?|gent/(?|clients/(?|edit/([^/]++)(*:144)|detail/([^/]++)(*:167))|application/export/pdf/([^/]++)(*:207))|dmin/(?|s(?|e(?|ttings/taxes/taxrates/edit/([^/]++)(*:267)|rvices/edit/([^/]++)(*:295))|taff/edit/([^/]++)(*:322)|ubject(?|area/edit/([^/]++)(*:357)|/edit/([^/]++)(*:379))|ource/edit/([^/]++)(*:407)|ms/status/([^/]++)(*:433))|user(?|s/(?|edit(?|/([^/]++)(*:470)|client/([^/]++)(*:493))|view/([^/]++)(*:515))|type/edit/([^/]++)(*:542)|role/edit/([^/]++)(*:568))|c(?|ustomer/edit/([^/]++)(*:602)|ontact/edit/([^/]++)(*:630)|lients/(?|edit/([^/]++)(*:661)|changetype/([^/]++)/([^/]++)(*:697)|detail/([^/]++)(*:720)|saveaccountreport/([^/]++)(*:754)|printpreview/([^/]++)(*:783))|hecklist/edit/([^/]++)(*:814)|rm_email_template/edit/([^/]++)(*:853))|lead(?|s/(?|history/([^/]++)(*:890)|edit/([^/]++)(*:911)|notes/delete/([^/]++)(*:940)|pin/([^/]++)(*:960))|\\-service/edit/([^/]++)(*:992))|invoice/(?|lists/([^/]++)(*:1026)|e(?|dit/([^/]++)(*:1051)|mail/([^/]++)(*:1073))|download/([^/]++)(*:1100)|pr(?|int/([^/]++)(*:1126)|eview/([^/]++)(*:1149))|reminder/([^/]++)(*:1176)|view/([^/]++)(*:1198))|e(?|dit_(?|email_template/([^/]++)(*:1242)|seo/([^/]++)(*:1263))|nquir(?|ysource/edit/([^/]++)(*:1302)|ies/(?|covertenquiry/([^/]++)(*:1340)|archived/([^/]++)(*:1366)))|mails/edit/([^/]++)(*:1396))|offer/edit/([^/]++)(*:1425)|d(?|ocument(?|/download/pdf/([^/]++)(*:1470)|checklist/edit/([^/]++)(*:1502))|estroy_(?|by_me/([^/]++)(*:1536)|to_me/([^/]++)(*:1559)|activity/([^/]++)(*:1585)|complete_activity/([^/]++)(*:1620)))|p(?|ro(?|duct(?|s/(?|edit/([^/]++)(*:1665)|detail/([^/]++)(*:1689))|\\-type/edit/([^/]++)(*:1719))|files/edit/([^/]++)(*:1748)|mo\\-code/edit/([^/]++)(*:1779))|a(?|rtner(?|s/(?|edit/([^/]++)(*:1819)|detail/([^/]++)(*:1843)|savepartner(?|studentinvoice/([^/]++)(*:1889)|record(?|invoice/([^/]++)(*:1923)|payment/([^/]++)(*:1948)))|printpreviewcreateinvoice/([^/]++)(*:1993)|updatecommission(?|percentage/([^/]++)(*:2040)|claimed/([^/]++)(*:2065)))|\\-type/edit/([^/]++)(*:2096))|yment/view/([^/]++)(*:2125)))|branch/(?|edit/([^/]++)(*:2159)|view/(?|([^/]++)(*:2184)|client/([^/]++)(*:2208)))|quotation(?|s/(?|client/create/([^/]++)(*:2258)|edit/([^/]++)(*:2280)|template/edit/([^/]++)(*:2311))|/(?|detail/([^/]++)(*:2340)|preview/([^/]++)(*:2365)))|a(?|gent(?|s/(?|show/([^/]++)(*:2405)|edit/([^/]++)(*:2427))|/detail/([^/]++)(*:2453))|pplication(?|s/(?|detail/([^/]++)(*:2496)|preview\\-schedules/([^/]++)(*:2532))|/(?|invoice/([^/]++)/([^/]++)/([^/]++)(*:2580)|export/pdf/([^/]++)(*:2608)))|ssignee/([^/]++)(?|(*:2638)|/edit(*:2652)|(*:2661)))|t(?|a(?|sks/archive/([^/]++)(*:2700)|x/edit/([^/]++)(*:2724)|gs/edit/([^/]++)(*:2749))|eams/edit/([^/]++)(*:2777))|visa\\-type/edit/([^/]++)(*:2811)|master\\-category/edit/([^/]++)(*:2850)|feetype/edit/([^/]++)(*:2880)|workflow/(?|edit/([^/]++)(*:2914)|deactivate\\-workflow/([^/]++)(*:2952)|activate\\-workflow/([^/]++)(*:2988))))|/e(?|mail\\-verify\\-token/([^/]++)(*:3033)|ditclient/([^/]++)(*:3060))|/verify\\-dob/([^/]++)(*:3091))/?$}sDu',
    ),
    3 => 
    array (
      32 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'passport.tokens.destroy',
          ),
          1 => 
          array (
            0 => 'token_id',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      58 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'passport.clients.update',
          ),
          1 => 
          array (
            0 => 'client_id',
          ),
          2 => 
          array (
            'PUT' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'passport.clients.destroy',
          ),
          1 => 
          array (
            0 => 'client_id',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      99 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'passport.personal.tokens.destroy',
          ),
          1 => 
          array (
            0 => 'token_id',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      144 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'agent.clients.edit',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      167 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'agent.clients.detail',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      207 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::LeqcymOtyBCInlyZ',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      267 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.edittaxrates',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      295 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.services.edit',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      322 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.staff.edit',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      357 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.feature.subjectarea.edit',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      379 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.feature.subject.edit',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      407 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.feature.source.edit',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      433 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'sms.status',
          ),
          1 => 
          array (
            0 => 'messageId',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      470 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.users.edit',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      493 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.users.editclient',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      515 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.users.view',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      542 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.usertype.edit',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      568 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.userrole.edit',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      602 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.customer.edit',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      630 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.managecontact.edit',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      661 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.edit',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      697 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::d94X12n8DALtYFBf',
          ),
          1 => 
          array (
            0 => 'id',
            1 => 'type',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      720 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.detail',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      754 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.clients.saveaccountreport',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      783 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::4ImaMUqDbQNHaUp6',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      814 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.checklist.edit',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      853 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.crmemailtemplate.edit',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      890 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.leads.history',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      911 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.leads.edit',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      940 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::5rByZqPDejHujxAC',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      960 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::L0KzHagz6oEjdndu',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      992 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.feature.leadservice.edit',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1026 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.invoice.lists',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1051 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::x2dz8vZCPpLF45d6',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1073 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.invoice.email',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1100 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.invoice.customer_invoice_download',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1126 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.invoice.customer_invoice_print',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1149 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::4Oen46w6pmBLl5nv',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1176 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.invoice.reminder',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1198 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::9oNfjakHlnexbMDi',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1242 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.edit_email_template',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1263 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.edit_seo',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1302 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.enquirysource.edit',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1340 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::RGpJgRdWiIQHwf0t',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1366 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::NTvUWWNoKkpUCtBy',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1396 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.emails.edit',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1425 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.offer.edit',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1470 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::FQqjBWzNMObM0RSR',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1502 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.feature.documentchecklist.edit',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1536 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'assignee.destroy_by_me',
          ),
          1 => 
          array (
            0 => 'note_id',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1559 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'assignee.destroy_to_me',
          ),
          1 => 
          array (
            0 => 'note_id',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1585 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'assignee.destroy_activity',
          ),
          1 => 
          array (
            0 => 'note_id',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1620 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'assignee.destroy_complete_activity',
          ),
          1 => 
          array (
            0 => 'note_id',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1665 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.products.edit',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1689 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.products.detail',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1719 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.feature.producttype.edit',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1748 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.feature.profiles.edit',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1779 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.feature.promocode.edit',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1819 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.partners.edit',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1843 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.partners.detail',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1889 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.partners.savepartnerstudentinvoice',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1923 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.partners.savepartnerrecordinvoice',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1948 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.partners.savepartnerrecordpayment',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1993 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::HIWsowVbao20uVV4',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      2040 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.partners.updatecommissionpercentage',
          ),
          1 => 
          array (
            0 => 'partner_id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      2065 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.partners.updatecommissionclaimed',
          ),
          1 => 
          array (
            0 => 'partner_id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      2096 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.feature.partnertype.edit',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      2125 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::qBXnfsx0UdSqU4jn',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      2159 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.branch.edit',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      2184 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.branch.userview',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      2208 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.branch.clientview',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      2258 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.quotations.create',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      2280 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.quotations.edit',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      2311 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.quotations.template.edit',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      2340 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::3Cx37nFxztO78azW',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      2365 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::s2KZ26k4UcpN738Z',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      2405 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.agents.show',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      2427 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.agents.edit',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      2453 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.agents.detail',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      2496 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.applications.detail',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      2532 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::TCDeqsrILSKUMl0u',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      2580 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::qCXOOEgFRwmKZS55',
          ),
          1 => 
          array (
            0 => 'client_id',
            1 => 'application',
            2 => 'invoice_type',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      2608 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::9JGVeJHBxVIxF560',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      2638 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'assignee.show',
          ),
          1 => 
          array (
            0 => 'assignee',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      2652 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'assignee.edit',
          ),
          1 => 
          array (
            0 => 'assignee',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      2661 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'assignee.update',
          ),
          1 => 
          array (
            0 => 'assignee',
          ),
          2 => 
          array (
            'PUT' => 0,
            'PATCH' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'assignee.destroy',
          ),
          1 => 
          array (
            0 => 'assignee',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      2700 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.tasks.archive',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      2724 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.feature.tax.edit',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      2749 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.feature.tags.edit',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      2777 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.teams.edit',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      2811 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.feature.visatype.edit',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      2850 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.feature.mastercategory.edit',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      2880 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.feetype.edit',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      2914 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.workflow.edit',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      2952 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.workflow.deactivate',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      2988 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.workflow.activate',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      3033 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'emailVerifyToken',
          ),
          1 => 
          array (
            0 => 'token',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      3060 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::5JyGFXV0XtVm9cz0',
          ),
          1 => 
          array (
            0 => 'encoded_id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      3091 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::6tIo0m239VsceOIK',
          ),
          1 => 
          array (
            0 => 'encoded_id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        1 => 
        array (
          0 => NULL,
          1 => NULL,
          2 => NULL,
          3 => NULL,
          4 => false,
          5 => false,
          6 => 0,
        ),
      ),
    ),
    4 => NULL,
  ),
  'attributes' => 
  array (
    'passport.token' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'oauth/token',
      'action' => 
      array (
        'uses' => 'Laravel\\Passport\\Http\\Controllers\\AccessTokenController@issueToken',
        'as' => 'passport.token',
        'middleware' => 'throttle',
        'controller' => 'Laravel\\Passport\\Http\\Controllers\\AccessTokenController@issueToken',
        'namespace' => 'Laravel\\Passport\\Http\\Controllers',
        'prefix' => 'oauth',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'passport.authorizations.authorize' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'oauth/authorize',
      'action' => 
      array (
        'uses' => 'Laravel\\Passport\\Http\\Controllers\\AuthorizationController@authorize',
        'as' => 'passport.authorizations.authorize',
        'middleware' => 'web',
        'controller' => 'Laravel\\Passport\\Http\\Controllers\\AuthorizationController@authorize',
        'namespace' => 'Laravel\\Passport\\Http\\Controllers',
        'prefix' => 'oauth',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'passport.token.refresh' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'oauth/token/refresh',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:web',
        ),
        'uses' => 'Laravel\\Passport\\Http\\Controllers\\TransientTokenController@refresh',
        'as' => 'passport.token.refresh',
        'controller' => 'Laravel\\Passport\\Http\\Controllers\\TransientTokenController@refresh',
        'namespace' => 'Laravel\\Passport\\Http\\Controllers',
        'prefix' => 'oauth',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'passport.authorizations.approve' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'oauth/authorize',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:web',
        ),
        'uses' => 'Laravel\\Passport\\Http\\Controllers\\ApproveAuthorizationController@approve',
        'as' => 'passport.authorizations.approve',
        'controller' => 'Laravel\\Passport\\Http\\Controllers\\ApproveAuthorizationController@approve',
        'namespace' => 'Laravel\\Passport\\Http\\Controllers',
        'prefix' => 'oauth',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'passport.authorizations.deny' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'oauth/authorize',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:web',
        ),
        'uses' => 'Laravel\\Passport\\Http\\Controllers\\DenyAuthorizationController@deny',
        'as' => 'passport.authorizations.deny',
        'controller' => 'Laravel\\Passport\\Http\\Controllers\\DenyAuthorizationController@deny',
        'namespace' => 'Laravel\\Passport\\Http\\Controllers',
        'prefix' => 'oauth',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'passport.tokens.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'oauth/tokens',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:web',
        ),
        'uses' => 'Laravel\\Passport\\Http\\Controllers\\AuthorizedAccessTokenController@forUser',
        'as' => 'passport.tokens.index',
        'controller' => 'Laravel\\Passport\\Http\\Controllers\\AuthorizedAccessTokenController@forUser',
        'namespace' => 'Laravel\\Passport\\Http\\Controllers',
        'prefix' => 'oauth',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'passport.tokens.destroy' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'oauth/tokens/{token_id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:web',
        ),
        'uses' => 'Laravel\\Passport\\Http\\Controllers\\AuthorizedAccessTokenController@destroy',
        'as' => 'passport.tokens.destroy',
        'controller' => 'Laravel\\Passport\\Http\\Controllers\\AuthorizedAccessTokenController@destroy',
        'namespace' => 'Laravel\\Passport\\Http\\Controllers',
        'prefix' => 'oauth',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'passport.clients.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'oauth/clients',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:web',
        ),
        'uses' => 'Laravel\\Passport\\Http\\Controllers\\ClientController@forUser',
        'as' => 'passport.clients.index',
        'controller' => 'Laravel\\Passport\\Http\\Controllers\\ClientController@forUser',
        'namespace' => 'Laravel\\Passport\\Http\\Controllers',
        'prefix' => 'oauth',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'passport.clients.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'oauth/clients',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:web',
        ),
        'uses' => 'Laravel\\Passport\\Http\\Controllers\\ClientController@store',
        'as' => 'passport.clients.store',
        'controller' => 'Laravel\\Passport\\Http\\Controllers\\ClientController@store',
        'namespace' => 'Laravel\\Passport\\Http\\Controllers',
        'prefix' => 'oauth',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'passport.clients.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
      ),
      'uri' => 'oauth/clients/{client_id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:web',
        ),
        'uses' => 'Laravel\\Passport\\Http\\Controllers\\ClientController@update',
        'as' => 'passport.clients.update',
        'controller' => 'Laravel\\Passport\\Http\\Controllers\\ClientController@update',
        'namespace' => 'Laravel\\Passport\\Http\\Controllers',
        'prefix' => 'oauth',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'passport.clients.destroy' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'oauth/clients/{client_id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:web',
        ),
        'uses' => 'Laravel\\Passport\\Http\\Controllers\\ClientController@destroy',
        'as' => 'passport.clients.destroy',
        'controller' => 'Laravel\\Passport\\Http\\Controllers\\ClientController@destroy',
        'namespace' => 'Laravel\\Passport\\Http\\Controllers',
        'prefix' => 'oauth',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'passport.scopes.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'oauth/scopes',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:web',
        ),
        'uses' => 'Laravel\\Passport\\Http\\Controllers\\ScopeController@all',
        'as' => 'passport.scopes.index',
        'controller' => 'Laravel\\Passport\\Http\\Controllers\\ScopeController@all',
        'namespace' => 'Laravel\\Passport\\Http\\Controllers',
        'prefix' => 'oauth',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'passport.personal.tokens.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'oauth/personal-access-tokens',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:web',
        ),
        'uses' => 'Laravel\\Passport\\Http\\Controllers\\PersonalAccessTokenController@forUser',
        'as' => 'passport.personal.tokens.index',
        'controller' => 'Laravel\\Passport\\Http\\Controllers\\PersonalAccessTokenController@forUser',
        'namespace' => 'Laravel\\Passport\\Http\\Controllers',
        'prefix' => 'oauth',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'passport.personal.tokens.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'oauth/personal-access-tokens',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:web',
        ),
        'uses' => 'Laravel\\Passport\\Http\\Controllers\\PersonalAccessTokenController@store',
        'as' => 'passport.personal.tokens.store',
        'controller' => 'Laravel\\Passport\\Http\\Controllers\\PersonalAccessTokenController@store',
        'namespace' => 'Laravel\\Passport\\Http\\Controllers',
        'prefix' => 'oauth',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'passport.personal.tokens.destroy' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'oauth/personal-access-tokens/{token_id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:web',
        ),
        'uses' => 'Laravel\\Passport\\Http\\Controllers\\PersonalAccessTokenController@destroy',
        'as' => 'passport.personal.tokens.destroy',
        'controller' => 'Laravel\\Passport\\Http\\Controllers\\PersonalAccessTokenController@destroy',
        'namespace' => 'Laravel\\Passport\\Http\\Controllers',
        'prefix' => 'oauth',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'sanctum.csrf-cookie' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'sanctum/csrf-cookie',
      'action' => 
      array (
        'uses' => 'Laravel\\Sanctum\\Http\\Controllers\\CsrfCookieController@show',
        'controller' => 'Laravel\\Sanctum\\Http\\Controllers\\CsrfCookieController@show',
        'namespace' => NULL,
        'prefix' => 'sanctum',
        'where' => 
        array (
        ),
        'middleware' => 
        array (
          0 => 'web',
        ),
        'as' => 'sanctum.csrf-cookie',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'ignition.healthCheck' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => '_ignition/health-check',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'Spatie\\LaravelIgnition\\Http\\Middleware\\RunnableSolutionsEnabled',
        ),
        'uses' => 'Spatie\\LaravelIgnition\\Http\\Controllers\\HealthCheckController@__invoke',
        'controller' => 'Spatie\\LaravelIgnition\\Http\\Controllers\\HealthCheckController',
        'as' => 'ignition.healthCheck',
        'namespace' => NULL,
        'prefix' => '_ignition',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'ignition.executeSolution' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => '_ignition/execute-solution',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'Spatie\\LaravelIgnition\\Http\\Middleware\\RunnableSolutionsEnabled',
        ),
        'uses' => 'Spatie\\LaravelIgnition\\Http\\Controllers\\ExecuteSolutionController@__invoke',
        'controller' => 'Spatie\\LaravelIgnition\\Http\\Controllers\\ExecuteSolutionController',
        'as' => 'ignition.executeSolution',
        'namespace' => NULL,
        'prefix' => '_ignition',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'ignition.updateConfig' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => '_ignition/update-config',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'Spatie\\LaravelIgnition\\Http\\Middleware\\RunnableSolutionsEnabled',
        ),
        'uses' => 'Spatie\\LaravelIgnition\\Http\\Controllers\\UpdateConfigController@__invoke',
        'controller' => 'Spatie\\LaravelIgnition\\Http\\Controllers\\UpdateConfigController',
        'as' => 'ignition.updateConfig',
        'namespace' => NULL,
        'prefix' => '_ignition',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::D0o6ilovrADNfbuN' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/login',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
        ),
        'uses' => 'App\\Http\\Controllers\\API\\AuthController@login',
        'controller' => 'App\\Http\\Controllers\\API\\AuthController@login',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'excluded_middleware' => 
        array (
          0 => 'throttle:api',
        ),
        'as' => 'generated::D0o6ilovrADNfbuN',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::crewhN8bn5AjFpDi' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'clear-cache',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'O:55:"Laravel\\SerializableClosure\\UnsignedSerializableClosure":1:{s:12:"serializable";O:46:"Laravel\\SerializableClosure\\Serializers\\Native":5:{s:3:"use";a:0:{}s:8:"function";s:327:"function() {

    \\Artisan::call(\'config:clear\');
	\\Artisan::call(\'view:clear\');
   $exitCode = \\Artisan::call(\'route:clear\');
   $exitCode = \\Artisan::call(\'route:cache\');
     /* $exitCode = \\Artisan::call(\'BirthDate:birthdate\');
        $output = \\Artisan::output();
        return $output;  */
    // return what you want
}";s:5:"scope";s:37:"Illuminate\\Routing\\RouteFileRegistrar";s:4:"this";N;s:4:"self";s:32:"0000000000000c1d0000000000000000";}}',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::crewhN8bn5AjFpDi',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'exception' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'POST',
        2 => 'HEAD',
      ),
      'uri' => 'exception',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\ExceptionController@index',
        'controller' => 'App\\Http\\Controllers\\ExceptionController@index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'exception',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'thankyou' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'thankyou',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\HomeController@thankyou',
        'controller' => 'App\\Http\\Controllers\\HomeController@thankyou',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'thankyou',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'agent.login' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'agent',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Auth\\AgentLoginController@showLoginForm',
        'controller' => 'App\\Http\\Controllers\\Auth\\AgentLoginController@showLoginForm',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/agent',
        'where' => 
        array (
        ),
        'as' => 'agent.login',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::OYAQcbEm1mfKuFyr' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'agent/login',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Auth\\AgentLoginController@showLoginForm',
        'controller' => 'App\\Http\\Controllers\\Auth\\AgentLoginController@showLoginForm',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/agent',
        'where' => 
        array (
        ),
        'as' => 'generated::OYAQcbEm1mfKuFyr',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::mP9k6XgBWsOBn7lo' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'agent/login',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Auth\\AgentLoginController@login',
        'controller' => 'App\\Http\\Controllers\\Auth\\AgentLoginController@login',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/agent',
        'where' => 
        array (
        ),
        'as' => 'generated::mP9k6XgBWsOBn7lo',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'agent.logout' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'agent/logout',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Auth\\AgentLoginController@logout',
        'controller' => 'App\\Http\\Controllers\\Auth\\AgentLoginController@logout',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/agent',
        'where' => 
        array (
        ),
        'as' => 'agent.logout',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'agent.dashboard' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'agent/dashboard',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Agent\\DashboardController@dashboard',
        'controller' => 'App\\Http\\Controllers\\Agent\\DashboardController@dashboard',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/agent',
        'where' => 
        array (
        ),
        'as' => 'agent.dashboard',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'agent.clients.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'agent/clients',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Agent\\ClientsController@index',
        'controller' => 'App\\Http\\Controllers\\Agent\\ClientsController@index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/agent',
        'where' => 
        array (
        ),
        'as' => 'agent.clients.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'agent.clients.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'agent/clients/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Agent\\ClientsController@create',
        'controller' => 'App\\Http\\Controllers\\Agent\\ClientsController@create',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/agent',
        'where' => 
        array (
        ),
        'as' => 'agent.clients.create',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'agent.clients.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'agent/clients/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Agent\\ClientsController@store',
        'controller' => 'App\\Http\\Controllers\\Agent\\ClientsController@store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/agent',
        'where' => 
        array (
        ),
        'as' => 'agent.clients.store',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'agent.clients.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'agent/clients/edit/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Agent\\ClientsController@edit',
        'controller' => 'App\\Http\\Controllers\\Agent\\ClientsController@edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/agent',
        'where' => 
        array (
        ),
        'as' => 'agent.clients.edit',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::zvZkVQx5kyjymI2c' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'agent/clients/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Agent\\ClientsController@edit',
        'controller' => 'App\\Http\\Controllers\\Agent\\ClientsController@edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/agent',
        'where' => 
        array (
        ),
        'as' => 'generated::zvZkVQx5kyjymI2c',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'agent.clients.detail' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'agent/clients/detail/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Agent\\ClientsController@detail',
        'controller' => 'App\\Http\\Controllers\\Agent\\ClientsController@detail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/agent',
        'where' => 
        array (
        ),
        'as' => 'agent.clients.detail',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'agent.clients.getrecipients' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'agent/clients/get-recipients',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Agent\\ClientsController@getrecipients',
        'controller' => 'App\\Http\\Controllers\\Agent\\ClientsController@getrecipients',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/agent',
        'where' => 
        array (
        ),
        'as' => 'agent.clients.getrecipients',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'agent.clients.getallclients' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'agent/clients/get-allclients',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Agent\\ClientsController@getallclients',
        'controller' => 'App\\Http\\Controllers\\Agent\\ClientsController@getallclients',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/agent',
        'where' => 
        array (
        ),
        'as' => 'agent.clients.getallclients',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'agent.clients.gettemplates' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'agent/get-templates',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Agent\\AdminController@gettemplates',
        'controller' => 'App\\Http\\Controllers\\Agent\\AdminController@gettemplates',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/agent',
        'where' => 
        array (
        ),
        'as' => 'agent.clients.gettemplates',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'agent.clients.sendmail' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'agent/sendmail',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Agent\\AdminController@sendmail',
        'controller' => 'App\\Http\\Controllers\\Agent\\AdminController@sendmail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/agent',
        'where' => 
        array (
        ),
        'as' => 'agent.clients.sendmail',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'agent.clients.createnote' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'agent/create-note',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Agent\\ClientsController@createnote',
        'controller' => 'App\\Http\\Controllers\\Agent\\ClientsController@createnote',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/agent',
        'where' => 
        array (
        ),
        'as' => 'agent.clients.createnote',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'agent.clients.getnotedetail' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'agent/getnotedetail',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Agent\\ClientsController@getnotedetail',
        'controller' => 'App\\Http\\Controllers\\Agent\\ClientsController@getnotedetail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/agent',
        'where' => 
        array (
        ),
        'as' => 'agent.clients.getnotedetail',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'agent.clients.deletenote' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'agent/deletenote',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Agent\\ClientsController@deletenote',
        'controller' => 'App\\Http\\Controllers\\Agent\\ClientsController@deletenote',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/agent',
        'where' => 
        array (
        ),
        'as' => 'agent.clients.deletenote',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'agent.clients.prospects' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'agent/prospects',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Agent\\ClientsController@prospects',
        'controller' => 'App\\Http\\Controllers\\Agent\\ClientsController@prospects',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/agent',
        'where' => 
        array (
        ),
        'as' => 'agent.clients.prospects',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::TdRTRDT81N4iG7lj' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'agent/viewnotedetail',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Agent\\ClientsController@viewnotedetail',
        'controller' => 'App\\Http\\Controllers\\Agent\\ClientsController@viewnotedetail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/agent',
        'where' => 
        array (
        ),
        'as' => 'generated::TdRTRDT81N4iG7lj',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::MCdZIiopbUT2VpuY' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'agent/viewapplicationnote',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Agent\\ClientsController@viewapplicationnote',
        'controller' => 'App\\Http\\Controllers\\Agent\\ClientsController@viewapplicationnote',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/agent',
        'where' => 
        array (
        ),
        'as' => 'generated::MCdZIiopbUT2VpuY',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'agent.clients.archived' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'agent/archived',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Agent\\ClientsController@archived',
        'controller' => 'App\\Http\\Controllers\\Agent\\ClientsController@archived',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/agent',
        'where' => 
        array (
        ),
        'as' => 'agent.clients.archived',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'agent.clients.updateclientstatus' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'agent/change-client-status',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Agent\\ClientsController@updateclientstatus',
        'controller' => 'App\\Http\\Controllers\\Agent\\ClientsController@updateclientstatus',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/agent',
        'where' => 
        array (
        ),
        'as' => 'agent.clients.updateclientstatus',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'agent.clients.activities' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'agent/get-activities',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Agent\\ClientsController@activities',
        'controller' => 'App\\Http\\Controllers\\Agent\\ClientsController@activities',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/agent',
        'where' => 
        array (
        ),
        'as' => 'agent.clients.activities',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'agent.clients.getapplicationlists' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'agent/get-application-lists',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Agent\\ClientsController@getapplicationlists',
        'controller' => 'App\\Http\\Controllers\\Agent\\ClientsController@getapplicationlists',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/agent',
        'where' => 
        array (
        ),
        'as' => 'agent.clients.getapplicationlists',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'agent.clients.saveapplication' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'agent/saveapplication',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Agent\\ClientsController@saveapplication',
        'controller' => 'App\\Http\\Controllers\\Agent\\ClientsController@saveapplication',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/agent',
        'where' => 
        array (
        ),
        'as' => 'agent.clients.saveapplication',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'agent.clients.getnotes' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'agent/get-notes',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Agent\\ClientsController@getnotes',
        'controller' => 'App\\Http\\Controllers\\Agent\\ClientsController@getnotes',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/agent',
        'where' => 
        array (
        ),
        'as' => 'agent.clients.getnotes',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'agent.clients.convertapplication' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'agent/convertapplication',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Agent\\ClientsController@convertapplication',
        'controller' => 'App\\Http\\Controllers\\Agent\\ClientsController@convertapplication',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/agent',
        'where' => 
        array (
        ),
        'as' => 'agent.clients.convertapplication',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'agent.clients.deleteservices' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'agent/deleteservices',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Agent\\ClientsController@deleteservices',
        'controller' => 'App\\Http\\Controllers\\Agent\\ClientsController@deleteservices',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/agent',
        'where' => 
        array (
        ),
        'as' => 'agent.clients.deleteservices',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'agent.clients.uploaddocument' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'agent/upload-document',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Agent\\ClientsController@uploaddocument',
        'controller' => 'App\\Http\\Controllers\\Agent\\ClientsController@uploaddocument',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/agent',
        'where' => 
        array (
        ),
        'as' => 'agent.clients.uploaddocument',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'agent.clients.deletedocs' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'agent/deletedocs',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Agent\\ClientsController@deletedocs',
        'controller' => 'App\\Http\\Controllers\\Agent\\ClientsController@deletedocs',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/agent',
        'where' => 
        array (
        ),
        'as' => 'agent.clients.deletedocs',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'agent.clients.renamedoc' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'agent/renamedoc',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Agent\\ClientsController@renamedoc',
        'controller' => 'App\\Http\\Controllers\\Agent\\ClientsController@renamedoc',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/agent',
        'where' => 
        array (
        ),
        'as' => 'agent.clients.renamedoc',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::TqJRFRCxciNUCVcc' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'agent/savetoapplication',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Agent\\ClientsController@savetoapplication',
        'controller' => 'App\\Http\\Controllers\\Agent\\ClientsController@savetoapplication',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/agent',
        'where' => 
        array (
        ),
        'as' => 'generated::TqJRFRCxciNUCVcc',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::Nu7xjP6fFyzxElRe' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'agent/interested-service',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Agent\\ClientsController@interestedService',
        'controller' => 'App\\Http\\Controllers\\Agent\\ClientsController@interestedService',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/agent',
        'where' => 
        array (
        ),
        'as' => 'generated::Nu7xjP6fFyzxElRe',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::5oSxS8hLHvvY9QmM' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'agent/edit-interested-service',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Agent\\ClientsController@editinterestedService',
        'controller' => 'App\\Http\\Controllers\\Agent\\ClientsController@editinterestedService',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/agent',
        'where' => 
        array (
        ),
        'as' => 'generated::5oSxS8hLHvvY9QmM',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::fTQ7MXyz4LQu0bxN' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'agent/showproductfeeserv',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Agent\\ClientsController@showproductfeeserv',
        'controller' => 'App\\Http\\Controllers\\Agent\\ClientsController@showproductfeeserv',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/agent',
        'where' => 
        array (
        ),
        'as' => 'generated::fTQ7MXyz4LQu0bxN',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::LROOj8Nqlxj7jfQ0' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'agent/servicesavefee',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Agent\\ClientsController@servicesavefee',
        'controller' => 'App\\Http\\Controllers\\Agent\\ClientsController@servicesavefee',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/agent',
        'where' => 
        array (
        ),
        'as' => 'generated::LROOj8Nqlxj7jfQ0',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::YkWMsw08Bjh8T3Je' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'agent/pinnote',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Agent\\ClientsController@pinnote',
        'controller' => 'App\\Http\\Controllers\\Agent\\ClientsController@pinnote',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/agent',
        'where' => 
        array (
        ),
        'as' => 'generated::YkWMsw08Bjh8T3Je',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::mmoWZkzSosQPSCjx' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'agent/getpartner',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Agent\\DashboardController@getpartner',
        'controller' => 'App\\Http\\Controllers\\Agent\\DashboardController@getpartner',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/agent',
        'where' => 
        array (
        ),
        'as' => 'generated::mmoWZkzSosQPSCjx',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'agent.quotations.getproduct' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'agent/getproduct',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Agent\\DashboardController@getproduct',
        'controller' => 'App\\Http\\Controllers\\Agent\\DashboardController@getproduct',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/agent',
        'where' => 
        array (
        ),
        'as' => 'agent.quotations.getproduct',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'agent.quotations.getbranch' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'agent/getbranch',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Agent\\DashboardController@getbranch',
        'controller' => 'App\\Http\\Controllers\\Agent\\DashboardController@getbranch',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/agent',
        'where' => 
        array (
        ),
        'as' => 'agent.quotations.getbranch',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::ChXupgBewSAzkHnY' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'agent/get-services',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Agent\\ClientsController@getServices',
        'controller' => 'App\\Http\\Controllers\\Agent\\ClientsController@getServices',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/agent',
        'where' => 
        array (
        ),
        'as' => 'generated::ChXupgBewSAzkHnY',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::oo7Be2X64X09gp3l' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'agent/getintrestedservice',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Agent\\ClientsController@getintrestedservice',
        'controller' => 'App\\Http\\Controllers\\Agent\\ClientsController@getintrestedservice',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/agent',
        'where' => 
        array (
        ),
        'as' => 'generated::oo7Be2X64X09gp3l',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::i8VlH776kOjTkg92' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'agent/getintrestedserviceedit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Agent\\ClientsController@getintrestedserviceedit',
        'controller' => 'App\\Http\\Controllers\\Agent\\ClientsController@getintrestedserviceedit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/agent',
        'where' => 
        array (
        ),
        'as' => 'generated::i8VlH776kOjTkg92',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::m7hKhqpWdtUUWGnD' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'agent/getapplicationdetail',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Agent\\ApplicationsController@getapplicationdetail',
        'controller' => 'App\\Http\\Controllers\\Agent\\ApplicationsController@getapplicationdetail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/agent',
        'where' => 
        array (
        ),
        'as' => 'generated::m7hKhqpWdtUUWGnD',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::LeqcymOtyBCInlyZ' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'agent/application/export/pdf/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Agent\\ApplicationsController@exportapplicationpdf',
        'controller' => 'App\\Http\\Controllers\\Agent\\ApplicationsController@exportapplicationpdf',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/agent',
        'where' => 
        array (
        ),
        'as' => 'generated::LeqcymOtyBCInlyZ',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::cgRdPhPXMx4yGUJZ' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'agent/getapplicationnotes',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Agent\\ApplicationsController@getapplicationnotes',
        'controller' => 'App\\Http\\Controllers\\Agent\\ApplicationsController@getapplicationnotes',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/agent',
        'where' => 
        array (
        ),
        'as' => 'generated::cgRdPhPXMx4yGUJZ',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.login' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Auth\\AdminLoginController@showLoginForm',
        'controller' => 'App\\Http\\Controllers\\Auth\\AdminLoginController@showLoginForm',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.login',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::ezHZGaVFO21tScag' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/login',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Auth\\AdminLoginController@showLoginForm',
        'controller' => 'App\\Http\\Controllers\\Auth\\AdminLoginController@showLoginForm',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::ezHZGaVFO21tScag',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::1UQ5grWzRyDdyU6A' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/login',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Auth\\AdminLoginController@login',
        'controller' => 'App\\Http\\Controllers\\Auth\\AdminLoginController@login',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::1UQ5grWzRyDdyU6A',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.logout' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/logout',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Auth\\AdminLoginController@logout',
        'controller' => 'App\\Http\\Controllers\\Auth\\AdminLoginController@logout',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.logout',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.dashboard' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/dashboard',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@dashboard',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@dashboard',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.dashboard',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.get_customer_detail' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/get_customer_detail',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@CustomerDetail',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@CustomerDetail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.get_customer_detail',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.my_profile' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/my_profile',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@myProfile',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@myProfile',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.my_profile',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::GyzFz4PQdPNaWgoF' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/my_profile',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@myProfile',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@myProfile',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::GyzFz4PQdPNaWgoF',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.change_password' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/change_password',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@change_password',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@change_password',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.change_password',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::Fvc9Ly3CWaXPqZ2t' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/change_password',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@change_password',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@change_password',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::Fvc9Ly3CWaXPqZ2t',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.sessions' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/sessions',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@sessions',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@sessions',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.sessions',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::qFqvUORqw2GV82JK' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/sessions',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@sessions',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@sessions',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::qFqvUORqw2GV82JK',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::8rSKLhJTPI0cPaRd' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/update_action',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@updateAction',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@updateAction',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::8rSKLhJTPI0cPaRd',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::D75FR1NAjq6iBEep' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/approved_action',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@approveAction',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@approveAction',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::D75FR1NAjq6iBEep',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::YwFpWpLcPVm7uT4U' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/process_action',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@processAction',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@processAction',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::YwFpWpLcPVm7uT4U',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::sJhH3WMOco1EznC4' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/archive_action',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@archiveAction',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@archiveAction',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::sJhH3WMOco1EznC4',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::knHc5Rmmp9Dpukk2' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/declined_action',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@declinedAction',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@declinedAction',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::knHc5Rmmp9Dpukk2',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::FCNt9DP2Ov4yjN5O' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/delete_action',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@deleteAction',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@deleteAction',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::FCNt9DP2Ov4yjN5O',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::KKaTvuXeOK5gvlhA' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/delete_slot_action',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@deleteSlotAction',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@deleteSlotAction',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::KKaTvuXeOK5gvlhA',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::Iu9e10Z6m28P1JOA' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/move_action',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@moveAction',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@moveAction',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::Iu9e10Z6m28P1JOA',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'add_ckeditior_image' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/add_ckeditior_image',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@addCkeditiorImage',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@addCkeditiorImage',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'add_ckeditior_image',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.get_chapters' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/get_chapters',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@getChapters',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@getChapters',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.get_chapters',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.website_setting' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/website_setting',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@websiteSetting',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@websiteSetting',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.website_setting',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::PHLZY8xjuT7VksOm' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/website_setting',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@websiteSetting',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@websiteSetting',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::PHLZY8xjuT7VksOm',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::aml4frH1gXJIy4tL' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/get_states',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@getStates',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@getStates',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::aml4frH1gXJIy4tL',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.returnsetting' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/settings/taxes/returnsetting',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@returnsetting',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@returnsetting',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.returnsetting',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.taxrates' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/settings/taxes/taxrates',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@taxrates',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@taxrates',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.taxrates',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.taxrates.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/settings/taxes/taxrates/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@taxratescreate',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@taxratescreate',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.taxrates.create',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.taxrates.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/settings/taxes/taxrates/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@savetaxrate',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@savetaxrate',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.taxrates.store',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.edittaxrates' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/settings/taxes/taxrates/edit/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@edittaxrates',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@edittaxrates',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.edittaxrates',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::rfn0i0phOQ9YvlvT' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/settings/taxes/taxrates/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@edittaxrates',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@edittaxrates',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::rfn0i0phOQ9YvlvT',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.savereturnsetting' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/settings/taxes/savereturnsetting',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@returnsetting',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@returnsetting',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.savereturnsetting',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::hvSNnX3P0JXlaerQ' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/getsubcategories',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@getsubcategories',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@getsubcategories',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::hvSNnX3P0JXlaerQ',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::CdB4f1WXnyyHFBPY' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/getproductbranch',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@getproductbranch',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@getproductbranch',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::CdB4f1WXnyyHFBPY',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::FwIHdXO1IWKX9k1V' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/getservicemodal',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ServicesController@servicemodal',
        'controller' => 'App\\Http\\Controllers\\Admin\\ServicesController@servicemodal',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::FwIHdXO1IWKX9k1V',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::nIsiohHbqGqK89GW' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/getassigneeajax',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@getassigneeajax',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@getassigneeajax',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::nIsiohHbqGqK89GW',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::wnrHf5QiGJZcKwqP' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/getpartnerajax',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@getpartnerajax',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@getpartnerajax',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::wnrHf5QiGJZcKwqP',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::k0GQEvxgxPfCDRTd' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/checkclientexist',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@checkclientexist',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@checkclientexist',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::k0GQEvxgxPfCDRTd',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.media.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/uploadfile/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\MediaController@store',
        'controller' => 'App\\Http\\Controllers\\Admin\\MediaController@store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.media.store',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.media.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/uploadfile/index',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\MediaController@index',
        'controller' => 'App\\Http\\Controllers\\Admin\\MediaController@index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.media.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.media.delete' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/uploadfile/delete',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\MediaController@deleteAction',
        'controller' => 'App\\Http\\Controllers\\Admin\\MediaController@deleteAction',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.media.delete',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.users.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/users',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\UserController@index',
        'controller' => 'App\\Http\\Controllers\\Admin\\UserController@index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.users.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.users.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/users/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\UserController@create',
        'controller' => 'App\\Http\\Controllers\\Admin\\UserController@create',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.users.create',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.users.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/users/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\UserController@store',
        'controller' => 'App\\Http\\Controllers\\Admin\\UserController@store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.users.store',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.users.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/users/edit/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\UserController@edit',
        'controller' => 'App\\Http\\Controllers\\Admin\\UserController@edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.users.edit',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.users.view' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/users/view/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\UserController@view',
        'controller' => 'App\\Http\\Controllers\\Admin\\UserController@view',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.users.view',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::vPSD2AobEmCOmHzS' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/users/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\UserController@edit',
        'controller' => 'App\\Http\\Controllers\\Admin\\UserController@edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::vPSD2AobEmCOmHzS',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::pYV7Jmjt8IVPAV9P' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/users/savezone',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\UserController@savezone',
        'controller' => 'App\\Http\\Controllers\\Admin\\UserController@savezone',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::pYV7Jmjt8IVPAV9P',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.users.active' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/users/active',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\UserController@active',
        'controller' => 'App\\Http\\Controllers\\Admin\\UserController@active',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.users.active',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.users.inactive' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/users/inactive',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\UserController@inactive',
        'controller' => 'App\\Http\\Controllers\\Admin\\UserController@inactive',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.users.inactive',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.users.invited' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/users/invited',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\UserController@invited',
        'controller' => 'App\\Http\\Controllers\\Admin\\UserController@invited',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.users.invited',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.staff.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/staff',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\StaffController@index',
        'controller' => 'App\\Http\\Controllers\\Admin\\StaffController@index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.staff.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.staff.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/staff/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\StaffController@create',
        'controller' => 'App\\Http\\Controllers\\Admin\\StaffController@create',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.staff.create',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.staff.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/staff/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\StaffController@store',
        'controller' => 'App\\Http\\Controllers\\Admin\\StaffController@store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.staff.store',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.staff.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/staff/edit/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\StaffController@edit',
        'controller' => 'App\\Http\\Controllers\\Admin\\StaffController@edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.staff.edit',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::7WDIKyDbYnahlUyB' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/staff/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\StaffController@edit',
        'controller' => 'App\\Http\\Controllers\\Admin\\StaffController@edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::7WDIKyDbYnahlUyB',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.customer.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/customer',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\CustomerController@index',
        'controller' => 'App\\Http\\Controllers\\Admin\\CustomerController@index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.customer.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.customer.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/customer/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\CustomerController@create',
        'controller' => 'App\\Http\\Controllers\\Admin\\CustomerController@create',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.customer.create',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.customer.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/customer/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\CustomerController@store',
        'controller' => 'App\\Http\\Controllers\\Admin\\CustomerController@store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.customer.store',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.customer.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/customer/edit/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\CustomerController@edit',
        'controller' => 'App\\Http\\Controllers\\Admin\\CustomerController@edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.customer.edit',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::qq2lJthJVLy7SLCP' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/customer/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\CustomerController@edit',
        'controller' => 'App\\Http\\Controllers\\Admin\\CustomerController@edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::qq2lJthJVLy7SLCP',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.customer.upload' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/customer/upload',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\CustomerController@uploadcsv',
        'controller' => 'App\\Http\\Controllers\\Admin\\CustomerController@uploadcsv',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.customer.upload',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.users.clientlist' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/users/clientlist',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\UserController@clientlist',
        'controller' => 'App\\Http\\Controllers\\Admin\\UserController@clientlist',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.users.clientlist',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.users.createclient' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/users/createclient',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\UserController@createclient',
        'controller' => 'App\\Http\\Controllers\\Admin\\UserController@createclient',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.users.createclient',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.users.storeclient' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/users/storeclient',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\UserController@storeclient',
        'controller' => 'App\\Http\\Controllers\\Admin\\UserController@storeclient',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.users.storeclient',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.users.editclient' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/users/editclient/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\UserController@editclient',
        'controller' => 'App\\Http\\Controllers\\Admin\\UserController@editclient',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.users.editclient',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::U9MCObypmPjbd9rM' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/users/editclient',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\UserController@editclient',
        'controller' => 'App\\Http\\Controllers\\Admin\\UserController@editclient',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::U9MCObypmPjbd9rM',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.followup.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/followup/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\FollowupController@store',
        'controller' => 'App\\Http\\Controllers\\Admin\\FollowupController@store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.followup.store',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.followup.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/followup/list',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\FollowupController@index',
        'controller' => 'App\\Http\\Controllers\\Admin\\FollowupController@index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.followup.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.followup.compose' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/followup/compose',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\FollowupController@compose',
        'controller' => 'App\\Http\\Controllers\\Admin\\FollowupController@compose',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.followup.compose',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.usertype.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/usertype',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\UsertypeController@index',
        'controller' => 'App\\Http\\Controllers\\Admin\\UsertypeController@index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.usertype.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.usertype.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/usertype/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\UsertypeController@create',
        'controller' => 'App\\Http\\Controllers\\Admin\\UsertypeController@create',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.usertype.create',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.usertype.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/usertype/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\UsertypeController@store',
        'controller' => 'App\\Http\\Controllers\\Admin\\UsertypeController@store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.usertype.store',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.usertype.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/usertype/edit/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\UsertypeController@edit',
        'controller' => 'App\\Http\\Controllers\\Admin\\UsertypeController@edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.usertype.edit',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::92Vsb1UtF8oWWIF0' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/usertype/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\UsertypeController@edit',
        'controller' => 'App\\Http\\Controllers\\Admin\\UsertypeController@edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::92Vsb1UtF8oWWIF0',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.userrole.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/userrole',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\UserroleController@index',
        'controller' => 'App\\Http\\Controllers\\Admin\\UserroleController@index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.userrole.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.userrole.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/userrole/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\UserroleController@create',
        'controller' => 'App\\Http\\Controllers\\Admin\\UserroleController@create',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.userrole.create',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.userrole.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/userrole/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\UserroleController@store',
        'controller' => 'App\\Http\\Controllers\\Admin\\UserroleController@store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.userrole.store',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.userrole.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/userrole/edit/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\UserroleController@edit',
        'controller' => 'App\\Http\\Controllers\\Admin\\UserroleController@edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.userrole.edit',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::POKTNEpSh2o15Jt9' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/userrole/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\UserroleController@edit',
        'controller' => 'App\\Http\\Controllers\\Admin\\UserroleController@edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::POKTNEpSh2o15Jt9',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.services.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/services',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ServicesController@index',
        'controller' => 'App\\Http\\Controllers\\Admin\\ServicesController@index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.services.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.services.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/services/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ServicesController@create',
        'controller' => 'App\\Http\\Controllers\\Admin\\ServicesController@create',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.services.create',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.services.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/services/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ServicesController@store',
        'controller' => 'App\\Http\\Controllers\\Admin\\ServicesController@store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.services.store',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.services.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/services/edit/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ServicesController@edit',
        'controller' => 'App\\Http\\Controllers\\Admin\\ServicesController@edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.services.edit',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::oXRlhvMbjp4m3Muw' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/services/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ServicesController@edit',
        'controller' => 'App\\Http\\Controllers\\Admin\\ServicesController@edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::oXRlhvMbjp4m3Muw',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.managecontact.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/contact',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ContactController@index',
        'controller' => 'App\\Http\\Controllers\\Admin\\ContactController@index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.managecontact.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.managecontact.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/contact/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ContactController@create',
        'controller' => 'App\\Http\\Controllers\\Admin\\ContactController@create',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.managecontact.create',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.managecontact.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/managecontact/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ContactController@store',
        'controller' => 'App\\Http\\Controllers\\Admin\\ContactController@store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.managecontact.store',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.managecontact.add' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/contact/add',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ContactController@add',
        'controller' => 'App\\Http\\Controllers\\Admin\\ContactController@add',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.managecontact.add',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.managecontact.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/contact/edit/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ContactController@edit',
        'controller' => 'App\\Http\\Controllers\\Admin\\ContactController@edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.managecontact.edit',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::2Gb74o1weYzXgr5G' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/contact/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ContactController@edit',
        'controller' => 'App\\Http\\Controllers\\Admin\\ContactController@edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::2Gb74o1weYzXgr5G',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::ktatWWEAS8paHsbj' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/contact/storeaddress',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ContactController@storeaddress',
        'controller' => 'App\\Http\\Controllers\\Admin\\ContactController@storeaddress',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::ktatWWEAS8paHsbj',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.leads.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/leads',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\LeadController@index',
        'controller' => 'App\\Http\\Controllers\\Admin\\LeadController@index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.leads.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.leads.history' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/leads/history/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\LeadController@history',
        'controller' => 'App\\Http\\Controllers\\Admin\\LeadController@history',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.leads.history',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.leads.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/leads/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\LeadController@create',
        'controller' => 'App\\Http\\Controllers\\Admin\\LeadController@create',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.leads.create',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.leads.assign' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/leads/assign',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\LeadController@assign',
        'controller' => 'App\\Http\\Controllers\\Admin\\LeadController@assign',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.leads.assign',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.leads.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/leads/edit/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\LeadController@edit',
        'controller' => 'App\\Http\\Controllers\\Admin\\LeadController@edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.leads.edit',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::7vZo7N26eXtDxF1k' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/leads/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\LeadController@edit',
        'controller' => 'App\\Http\\Controllers\\Admin\\LeadController@edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::7vZo7N26eXtDxF1k',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::5rByZqPDejHujxAC' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/leads/notes/delete/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\LeadController@leaddeleteNotes',
        'controller' => 'App\\Http\\Controllers\\Admin\\LeadController@leaddeleteNotes',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::5rByZqPDejHujxAC',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::jeEEcCUymkm8zhdm' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/get-notedetail',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\LeadController@getnotedetail',
        'controller' => 'App\\Http\\Controllers\\Admin\\LeadController@getnotedetail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::jeEEcCUymkm8zhdm',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::J6DIpsMPId6fHvjj' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/followup/update',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\FollowupController@followupupdate',
        'controller' => 'App\\Http\\Controllers\\Admin\\FollowupController@followupupdate',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::J6DIpsMPId6fHvjj',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.leads.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/leads/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\LeadController@store',
        'controller' => 'App\\Http\\Controllers\\Admin\\LeadController@store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.leads.store',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::m3qiYOGOSrTahWsq' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/leads/convert',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\LeadController@convertoClient',
        'controller' => 'App\\Http\\Controllers\\Admin\\LeadController@convertoClient',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::m3qiYOGOSrTahWsq',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::L0KzHagz6oEjdndu' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/leads/pin/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\LeadController@leadPin',
        'controller' => 'App\\Http\\Controllers\\Admin\\LeadController@leadPin',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::L0KzHagz6oEjdndu',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.invoice.lists' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/invoice/lists/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\InvoiceController@lists',
        'controller' => 'App\\Http\\Controllers\\Admin\\InvoiceController@lists',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.invoice.lists',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::x2dz8vZCPpLF45d6' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/invoice/edit/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\InvoiceController@edit',
        'controller' => 'App\\Http\\Controllers\\Admin\\InvoiceController@edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::x2dz8vZCPpLF45d6',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::XdefMvULNSyuuD2t' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/invoice/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\InvoiceController@edit',
        'controller' => 'App\\Http\\Controllers\\Admin\\InvoiceController@edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::XdefMvULNSyuuD2t',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.invoice.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/invoice/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\InvoiceController@create',
        'controller' => 'App\\Http\\Controllers\\Admin\\InvoiceController@create',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.invoice.create',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.invoice.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/invoice/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\InvoiceController@store',
        'controller' => 'App\\Http\\Controllers\\Admin\\InvoiceController@store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.invoice.store',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.invoice.detail' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/invoice/detail',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\InvoiceController@detail',
        'controller' => 'App\\Http\\Controllers\\Admin\\InvoiceController@detail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.invoice.detail',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.invoice.email' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/invoice/email/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\InvoiceController@email',
        'controller' => 'App\\Http\\Controllers\\Admin\\InvoiceController@email',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.invoice.email',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::6MJzHlqsuQXvp8ln' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/invoice/email',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\InvoiceController@email',
        'controller' => 'App\\Http\\Controllers\\Admin\\InvoiceController@email',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::6MJzHlqsuQXvp8ln',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.invoice.editpayment' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/invoice/editpayment',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\InvoiceController@editpayment',
        'controller' => 'App\\Http\\Controllers\\Admin\\InvoiceController@editpayment',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.invoice.editpayment',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.invoice.invoicebyid' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/invoice/invoicebyid',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\InvoiceController@invoicebyid',
        'controller' => 'App\\Http\\Controllers\\Admin\\InvoiceController@invoicebyid',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.invoice.invoicebyid',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.invoice.history' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/invoice/history',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\InvoiceController@history',
        'controller' => 'App\\Http\\Controllers\\Admin\\InvoiceController@history',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.invoice.history',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.invoice.paymentsave' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/invoice/paymentsave',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\InvoiceController@paymentsave',
        'controller' => 'App\\Http\\Controllers\\Admin\\InvoiceController@paymentsave',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.invoice.paymentsave',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.invoice.editpaymentsave' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/invoice/editpaymentsave',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\InvoiceController@editpaymentsave',
        'controller' => 'App\\Http\\Controllers\\Admin\\InvoiceController@editpaymentsave',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.invoice.editpaymentsave',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.invoice.addcomment' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/invoice/addcomment',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\InvoiceController@addcomment',
        'controller' => 'App\\Http\\Controllers\\Admin\\InvoiceController@addcomment',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.invoice.addcomment',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.invoice.sharelink' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/invoice/sharelink',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\InvoiceController@sharelink',
        'controller' => 'App\\Http\\Controllers\\Admin\\InvoiceController@sharelink',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.invoice.sharelink',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.invoice.disablelink' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/invoice/disablelink',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\InvoiceController@disablelink',
        'controller' => 'App\\Http\\Controllers\\Admin\\InvoiceController@disablelink',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.invoice.disablelink',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.invoice.customer_invoice_download' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/invoice/download/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\InvoiceController@customer_invoice_download',
        'controller' => 'App\\Http\\Controllers\\Admin\\InvoiceController@customer_invoice_download',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.invoice.customer_invoice_download',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.invoice.exportall' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/invoice/exportall',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\InvoiceController@exportall',
        'controller' => 'App\\Http\\Controllers\\Admin\\InvoiceController@exportall',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.invoice.exportall',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.invoice.customer_invoice_printall' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/invoice/printall',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\InvoiceController@customer_invoice_printall',
        'controller' => 'App\\Http\\Controllers\\Admin\\InvoiceController@customer_invoice_printall',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.invoice.customer_invoice_printall',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.invoice.customer_invoice_print' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/invoice/print/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\InvoiceController@customer_invoice_print',
        'controller' => 'App\\Http\\Controllers\\Admin\\InvoiceController@customer_invoice_print',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.invoice.customer_invoice_print',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.invoice.reminder' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/invoice/reminder/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\InvoiceController@reminder',
        'controller' => 'App\\Http\\Controllers\\Admin\\InvoiceController@reminder',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.invoice.reminder',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::fMhN7kwtFIOXMXLp' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/invoice/reminder',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\InvoiceController@reminder',
        'controller' => 'App\\Http\\Controllers\\Admin\\InvoiceController@reminder',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::fMhN7kwtFIOXMXLp',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.invoice.attachfile' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/invoice/attachfile',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\InvoiceController@attachfile',
        'controller' => 'App\\Http\\Controllers\\Admin\\InvoiceController@attachfile',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.invoice.attachfile',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.invoice.getattachfile' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/invoice/getattachfile',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\InvoiceController@getattachfile',
        'controller' => 'App\\Http\\Controllers\\Admin\\InvoiceController@getattachfile',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.invoice.getattachfile',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.invoice.removeattachfile' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/invoice/removeattachfile',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\InvoiceController@removeattachfile',
        'controller' => 'App\\Http\\Controllers\\Admin\\InvoiceController@removeattachfile',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.invoice.removeattachfile',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.invoice.attachfileemail' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/invoice/attachfileemail',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\InvoiceController@attachfileemail',
        'controller' => 'App\\Http\\Controllers\\Admin\\InvoiceController@attachfileemail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.invoice.attachfileemail',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.email.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/email_templates',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\EmailTemplateController@index',
        'controller' => 'App\\Http\\Controllers\\Admin\\EmailTemplateController@index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.email.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.email.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/email_templates/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\EmailTemplateController@create',
        'controller' => 'App\\Http\\Controllers\\Admin\\EmailTemplateController@create',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.email.create',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.email.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/email_templates/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\EmailTemplateController@store',
        'controller' => 'App\\Http\\Controllers\\Admin\\EmailTemplateController@store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.email.store',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.edit_email_template' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/edit_email_template/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\EmailTemplateController@editEmailTemplate',
        'controller' => 'App\\Http\\Controllers\\Admin\\EmailTemplateController@editEmailTemplate',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.edit_email_template',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::a4rEame8LbZlfMe8' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/edit_email_template',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\EmailTemplateController@editEmailTemplate',
        'controller' => 'App\\Http\\Controllers\\Admin\\EmailTemplateController@editEmailTemplate',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::a4rEame8LbZlfMe8',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.edit_seo' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/edit_seo/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@editSeo',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@editSeo',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.edit_seo',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::NXmkbcDeDpcATWkj' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/edit_seo',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@editSeo',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@editSeo',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::NXmkbcDeDpcATWkj',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.edit_api' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/api-key',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@editapi',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@editapi',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.edit_api',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::YtsvKq0fvxwm09Qe' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/api-key',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@editapi',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@editapi',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::YtsvKq0fvxwm09Qe',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.offer.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/offer/index',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\OfferController@index',
        'controller' => 'App\\Http\\Controllers\\Admin\\OfferController@index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.offer.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.offer.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/offer/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\OfferController@create',
        'controller' => 'App\\Http\\Controllers\\Admin\\OfferController@create',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.offer.create',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.offer.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/offer/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\OfferController@store',
        'controller' => 'App\\Http\\Controllers\\Admin\\OfferController@store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.offer.store',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.offer.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/offer/edit/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\OfferController@edit',
        'controller' => 'App\\Http\\Controllers\\Admin\\OfferController@edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.offer.edit',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::R7SuTVVWOdsxe1zb' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/offer/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\OfferController@edit',
        'controller' => 'App\\Http\\Controllers\\Admin\\OfferController@edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::R7SuTVVWOdsxe1zb',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.photo.getlist' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/photo-gallery/getlist',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\MediaController@getlist',
        'controller' => 'App\\Http\\Controllers\\Admin\\MediaController@getlist',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.photo.getlist',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.photo.uploadlist' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/photo-gallery/uploadlist',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\MediaController@uploadlist',
        'controller' => 'App\\Http\\Controllers\\Admin\\MediaController@uploadlist',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.photo.uploadlist',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.photo.update_action' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/photo-gallery/update_action',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\MediaController@update_action',
        'controller' => 'App\\Http\\Controllers\\Admin\\MediaController@update_action',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.photo.update_action',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/clients',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@index',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/clients/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@create',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@create',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.create',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/clients/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@store',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.store',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/clients/edit/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@edit',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.edit',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::RStQXYyHxBjTvf9n' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/clients/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@edit',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::RStQXYyHxBjTvf9n',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::aRTYSgOzH39dQbfF' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/clients/followup/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@followupstore',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@followupstore',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::aRTYSgOzH39dQbfF',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::qJViyjeP7wMpJwUS' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/clients/followup_application/store_application',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@followupstore_application',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@followupstore_application',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::qJViyjeP7wMpJwUS',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::JuWyJRPHNuCj1cVt' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/clients/followup/retagfollowup',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@retagfollowup',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@retagfollowup',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::JuWyJRPHNuCj1cVt',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::d94X12n8DALtYFBf' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/clients/changetype/{id}/{type}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@changetype',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@changetype',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::d94X12n8DALtYFBf',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::FQqjBWzNMObM0RSR' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/document/download/pdf/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@downloadpdf',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@downloadpdf',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::FQqjBWzNMObM0RSR',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::s4GQDl3O2nia9ZgQ' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/clients/removetag',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@removetag',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@removetag',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::s4GQDl3O2nia9ZgQ',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.detail' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/clients/detail/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@detail',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@detail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.detail',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.getrecipients' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/clients/get-recipients',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@getrecipients',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@getrecipients',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.getrecipients',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.getonlyclientrecipients' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/clients/get-onlyclientrecipients',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@getonlyclientrecipients',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@getonlyclientrecipients',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.getonlyclientrecipients',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.getallclients' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/clients/get-allclients',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@getallclients',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@getallclients',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.getallclients',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::wFmkQAjm6CNddyxq' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/clients/change_assignee',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@change_assignee',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@change_assignee',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::wFmkQAjm6CNddyxq',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.gettemplates' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/get-templates',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@gettemplates',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@gettemplates',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.gettemplates',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.sendmail' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/sendmail',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@sendmail',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@sendmail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.sendmail',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.createnote' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/create-note',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@createnote',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@createnote',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.createnote',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.getnotedetail' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/getnotedetail',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@getnotedetail',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@getnotedetail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.getnotedetail',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.deletenote' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/deletenote',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@deletenote',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@deletenote',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.deletenote',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.deleteactivitylog' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/deleteactivitylog',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@deleteactivitylog',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@deleteactivitylog',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.deleteactivitylog',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.notpickedcall' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/not-picked-call',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@notpickedcall',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@notpickedcall',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.notpickedcall',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.prospects' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/prospects',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@prospects',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@prospects',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.prospects',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::OIGYprXwIXXMQOpu' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/viewnotedetail',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@viewnotedetail',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@viewnotedetail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::OIGYprXwIXXMQOpu',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::Trkzpj1X6BEOR1RE' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/viewapplicationnote',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@viewapplicationnote',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@viewapplicationnote',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::Trkzpj1X6BEOR1RE',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::b3mUfZXwJRBMW6Pu' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/saveprevvisa',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@saveprevvisa',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@saveprevvisa',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::b3mUfZXwJRBMW6Pu',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::TBUHlXm0jsRByEBi' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/saveonlineprimaryform',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@saveonlineform',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@saveonlineform',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::TBUHlXm0jsRByEBi',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::KMqVkve6zZnZztMz' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/saveonlinesecform',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@saveonlineform',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@saveonlineform',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::KMqVkve6zZnZztMz',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::GIYUm2POrGwl1Zlq' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/saveonlinechildform',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@saveonlineform',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@saveonlineform',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::GIYUm2POrGwl1Zlq',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.archived' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/archived',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@archived',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@archived',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.archived',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.updateclientstatus' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/change-client-status',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@updateclientstatus',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@updateclientstatus',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.updateclientstatus',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.activities' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/get-activities',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@activities',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@activities',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.activities',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.getapplicationlists' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/get-application-lists',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@getapplicationlists',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@getapplicationlists',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.getapplicationlists',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.saveapplication' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/saveapplication',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@saveapplication',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@saveapplication',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.saveapplication',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.getnotes' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/get-notes',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@getnotes',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@getnotes',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.getnotes',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.convertapplication' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/convertapplication',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@convertapplication',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@convertapplication',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.convertapplication',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.deleteservices' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/deleteservices',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@deleteservices',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@deleteservices',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.deleteservices',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.uploaddocument' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/upload-document',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@uploaddocument',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@uploaddocument',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.uploaddocument',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.deletedocs' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/deletedocs',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@deletedocs',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@deletedocs',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.deletedocs',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.renamedoc' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/renamedoc',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@renamedoc',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@renamedoc',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.renamedoc',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::J13LXUf7hkKcD4cq' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/savetoapplication',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@savetoapplication',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@savetoapplication',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::J13LXUf7hkKcD4cq',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.products.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/products',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ProductsController@index',
        'controller' => 'App\\Http\\Controllers\\Admin\\ProductsController@index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.products.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.products.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/products/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ProductsController@create',
        'controller' => 'App\\Http\\Controllers\\Admin\\ProductsController@create',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.products.create',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.products.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/products/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ProductsController@store',
        'controller' => 'App\\Http\\Controllers\\Admin\\ProductsController@store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.products.store',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.products.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/products/edit/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ProductsController@edit',
        'controller' => 'App\\Http\\Controllers\\Admin\\ProductsController@edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.products.edit',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::9cAydT5dNix71pyA' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/products/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ProductsController@edit',
        'controller' => 'App\\Http\\Controllers\\Admin\\ProductsController@edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::9cAydT5dNix71pyA',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.products.import' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/products-import',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ProductsController@import',
        'controller' => 'App\\Http\\Controllers\\Admin\\ProductsController@import',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.products.import',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.products.detail' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/products/detail/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ProductsController@detail',
        'controller' => 'App\\Http\\Controllers\\Admin\\ProductsController@detail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.products.detail',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.products.getrecipients' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/products/get-recipients',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ProductsController@getrecipients',
        'controller' => 'App\\Http\\Controllers\\Admin\\ProductsController@getrecipients',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.products.getrecipients',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.products.getallclients' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/products/get-allclients',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ProductsController@getallclients',
        'controller' => 'App\\Http\\Controllers\\Admin\\ProductsController@getallclients',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.products.getallclients',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.partners.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/partners',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\PartnersController@index',
        'controller' => 'App\\Http\\Controllers\\Admin\\PartnersController@index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.partners.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.partners.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/partners/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\PartnersController@create',
        'controller' => 'App\\Http\\Controllers\\Admin\\PartnersController@create',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.partners.create',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.partners.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/partners/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\PartnersController@store',
        'controller' => 'App\\Http\\Controllers\\Admin\\PartnersController@store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.partners.store',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.partners.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/partners/edit/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\PartnersController@edit',
        'controller' => 'App\\Http\\Controllers\\Admin\\PartnersController@edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.partners.edit',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::CPdwmc3lFz77kZHl' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/partners/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\PartnersController@edit',
        'controller' => 'App\\Http\\Controllers\\Admin\\PartnersController@edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::CPdwmc3lFz77kZHl',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.partners.getpaymenttype' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/getpaymenttype',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\PartnersController@getpaymenttype',
        'controller' => 'App\\Http\\Controllers\\Admin\\PartnersController@getpaymenttype',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.partners.getpaymenttype',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.partners.detail' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/partners/detail/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\PartnersController@detail',
        'controller' => 'App\\Http\\Controllers\\Admin\\PartnersController@detail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.partners.detail',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.partners.getrecipients' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/partners/get-recipients',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\PartnersController@getrecipients',
        'controller' => 'App\\Http\\Controllers\\Admin\\PartnersController@getrecipients',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.partners.getrecipients',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.partners.getallclients' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/partners/get-allclients',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\PartnersController@getallclients',
        'controller' => 'App\\Http\\Controllers\\Admin\\PartnersController@getallclients',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.partners.getallclients',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.branch.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/branch',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\BranchesController@index',
        'controller' => 'App\\Http\\Controllers\\Admin\\BranchesController@index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.branch.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.branch.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/branch/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\BranchesController@create',
        'controller' => 'App\\Http\\Controllers\\Admin\\BranchesController@create',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.branch.create',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.branch.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/branch/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\BranchesController@store',
        'controller' => 'App\\Http\\Controllers\\Admin\\BranchesController@store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.branch.store',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.branch.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/branch/edit/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\BranchesController@edit',
        'controller' => 'App\\Http\\Controllers\\Admin\\BranchesController@edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.branch.edit',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.branch.userview' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/branch/view/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\BranchesController@view',
        'controller' => 'App\\Http\\Controllers\\Admin\\BranchesController@view',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.branch.userview',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.branch.clientview' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/branch/view/client/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\BranchesController@viewclient',
        'controller' => 'App\\Http\\Controllers\\Admin\\BranchesController@viewclient',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.branch.clientview',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::LozYAjeFZ92evAUP' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/branch/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\BranchesController@edit',
        'controller' => 'App\\Http\\Controllers\\Admin\\BranchesController@edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::LozYAjeFZ92evAUP',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.quotations.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/quotations',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\QuotationsController@index',
        'controller' => 'App\\Http\\Controllers\\Admin\\QuotationsController@index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.quotations.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.quotations.client' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/quotations/client',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\QuotationsController@client',
        'controller' => 'App\\Http\\Controllers\\Admin\\QuotationsController@client',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.quotations.client',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.quotations.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/quotations/client/create/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\QuotationsController@create',
        'controller' => 'App\\Http\\Controllers\\Admin\\QuotationsController@create',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.quotations.create',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.quotations.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/quotations/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\QuotationsController@store',
        'controller' => 'App\\Http\\Controllers\\Admin\\QuotationsController@store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.quotations.store',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.quotations.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/quotations/edit/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\QuotationsController@edit',
        'controller' => 'App\\Http\\Controllers\\Admin\\QuotationsController@edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.quotations.edit',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::nOVSkPmiwRoOIXzH' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/quotations/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\QuotationsController@edit',
        'controller' => 'App\\Http\\Controllers\\Admin\\QuotationsController@edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::nOVSkPmiwRoOIXzH',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.quotations.template.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/quotations/template',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\QuotationsController@template',
        'controller' => 'App\\Http\\Controllers\\Admin\\QuotationsController@template',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.quotations.template.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.quotations.template.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/quotations/template/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\QuotationsController@template_create',
        'controller' => 'App\\Http\\Controllers\\Admin\\QuotationsController@template_create',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.quotations.template.create',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.quotations.template.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/quotations/template/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\QuotationsController@template_store',
        'controller' => 'App\\Http\\Controllers\\Admin\\QuotationsController@template_store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.quotations.template.store',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.quotations.template.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/quotations/template/edit/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\QuotationsController@template_edit',
        'controller' => 'App\\Http\\Controllers\\Admin\\QuotationsController@template_edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.quotations.template.edit',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::kHKQIP0GB1SbjsFL' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/quotations/template/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\QuotationsController@template_edit',
        'controller' => 'App\\Http\\Controllers\\Admin\\QuotationsController@template_edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::kHKQIP0GB1SbjsFL',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::3Cx37nFxztO78azW' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/quotation/detail/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\QuotationsController@quotationDetail',
        'controller' => 'App\\Http\\Controllers\\Admin\\QuotationsController@quotationDetail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::3Cx37nFxztO78azW',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::s2KZ26k4UcpN738Z' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/quotation/preview/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\QuotationsController@quotationpreview',
        'controller' => 'App\\Http\\Controllers\\Admin\\QuotationsController@quotationpreview',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::s2KZ26k4UcpN738Z',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.quotations.archived' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/quotations/archived',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\QuotationsController@archived',
        'controller' => 'App\\Http\\Controllers\\Admin\\QuotationsController@archived',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.quotations.archived',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.quotations.changestatus' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/quotations/changestatus',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\QuotationsController@changestatus',
        'controller' => 'App\\Http\\Controllers\\Admin\\QuotationsController@changestatus',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.quotations.changestatus',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.quotations.sendmail' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/quotations/sendmail',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\QuotationsController@sendmail',
        'controller' => 'App\\Http\\Controllers\\Admin\\QuotationsController@sendmail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.quotations.sendmail',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.quotations.getpartner' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/getpartner',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@getpartner',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@getpartner',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.quotations.getpartner',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.quotations.getpartnerbranch' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/getpartnerbranch',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@getpartnerbranch',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@getpartnerbranch',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.quotations.getpartnerbranch',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::cDk00rBtn2IWTxzR' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/getsubjects',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@getsubjects',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@getsubjects',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::cDk00rBtn2IWTxzR',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.quotations.getbranchproduct' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/getbranchproduct',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@getbranchproduct',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@getbranchproduct',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.quotations.getbranchproduct',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.quotations.getproduct' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/getproduct',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@getproduct',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@getproduct',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.quotations.getproduct',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.quotations.getbranch' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/getbranch',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@getbranch',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@getbranch',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.quotations.getbranch',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.quotations.getnewPartnerbranch' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/getnewPartnerbranch',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@getnewPartnerbranch',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@getnewPartnerbranch',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.quotations.getnewPartnerbranch',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.agents.active' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/agents/active',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AgentController@active',
        'controller' => 'App\\Http\\Controllers\\Admin\\AgentController@active',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.agents.active',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.agents.inactive' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/agents/inactive',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AgentController@inactive',
        'controller' => 'App\\Http\\Controllers\\Admin\\AgentController@inactive',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.agents.inactive',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.agents.show' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/agents/show/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AgentController@show',
        'controller' => 'App\\Http\\Controllers\\Admin\\AgentController@show',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.agents.show',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.agents.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/agents/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AgentController@create',
        'controller' => 'App\\Http\\Controllers\\Admin\\AgentController@create',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.agents.create',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.agents.import' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/agents/import',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AgentController@import',
        'controller' => 'App\\Http\\Controllers\\Admin\\AgentController@import',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.agents.import',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.agents.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/agents/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AgentController@store',
        'controller' => 'App\\Http\\Controllers\\Admin\\AgentController@store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.agents.store',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.agents.detail' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/agent/detail/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AgentController@detail',
        'controller' => 'App\\Http\\Controllers\\Admin\\AgentController@detail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.agents.detail',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::nRZHdCTJP4TWsYZr' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/agents/savepartner',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AgentController@savepartner',
        'controller' => 'App\\Http\\Controllers\\Admin\\AgentController@savepartner',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::nRZHdCTJP4TWsYZr',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.agents.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/agents/edit/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AgentController@edit',
        'controller' => 'App\\Http\\Controllers\\Admin\\AgentController@edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.agents.edit',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::kvsvOelYcXz5owBe' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/agents/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AgentController@edit',
        'controller' => 'App\\Http\\Controllers\\Admin\\AgentController@edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::kvsvOelYcXz5owBe',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::U9JPxJ381oKwYmoZ' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/agents/import/business',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AgentController@businessimport',
        'controller' => 'App\\Http\\Controllers\\Admin\\AgentController@businessimport',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::U9JPxJ381oKwYmoZ',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::jnfA8LgQ5HMamcYW' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/agents/import/individual',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AgentController@individualimport',
        'controller' => 'App\\Http\\Controllers\\Admin\\AgentController@individualimport',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::jnfA8LgQ5HMamcYW',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.tasks.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/tasks',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\TasksController@index',
        'controller' => 'App\\Http\\Controllers\\Admin\\TasksController@index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.tasks.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.tasks.archive' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/tasks/archive/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\TasksController@taskArchive',
        'controller' => 'App\\Http\\Controllers\\Admin\\TasksController@taskArchive',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.tasks.archive',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.tasks.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/tasks/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\TasksController@create',
        'controller' => 'App\\Http\\Controllers\\Admin\\TasksController@create',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.tasks.create',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.tasks.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/tasks/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\TasksController@store',
        'controller' => 'App\\Http\\Controllers\\Admin\\TasksController@store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.tasks.store',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.tasks.groupstore' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/tasks/groupstore',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\TasksController@groupstore',
        'controller' => 'App\\Http\\Controllers\\Admin\\TasksController@groupstore',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.tasks.groupstore',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.tasks.deletegroup' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/tasks/deletegroup',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\TasksController@deletegroup',
        'controller' => 'App\\Http\\Controllers\\Admin\\TasksController@deletegroup',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.tasks.deletegroup',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.tasks.gettasks' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/get-tasks',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\TasksController@gettasks',
        'controller' => 'App\\Http\\Controllers\\Admin\\TasksController@gettasks',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.tasks.gettasks',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.tasks.gettaskdetail' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/get-task-detail',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\TasksController@taskdetail',
        'controller' => 'App\\Http\\Controllers\\Admin\\TasksController@taskdetail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.tasks.gettaskdetail',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::oel8DXgGHLChGHTW' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/update_task_comment',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\TasksController@update_task_comment',
        'controller' => 'App\\Http\\Controllers\\Admin\\TasksController@update_task_comment',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::oel8DXgGHLChGHTW',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::6NBbutEweCYY69pw' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/update_task_description',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\TasksController@update_task_description',
        'controller' => 'App\\Http\\Controllers\\Admin\\TasksController@update_task_description',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::6NBbutEweCYY69pw',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::y9sQp6ACA4NMBfce' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/update_task_status',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\TasksController@update_task_status',
        'controller' => 'App\\Http\\Controllers\\Admin\\TasksController@update_task_status',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::y9sQp6ACA4NMBfce',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::eQXxNzFQeYsJESyn' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/update_task_priority',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\TasksController@update_task_priority',
        'controller' => 'App\\Http\\Controllers\\Admin\\TasksController@update_task_priority',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::eQXxNzFQeYsJESyn',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::AhGCk6HWAObxkXMG' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/updateduedate',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\TasksController@updateduedate',
        'controller' => 'App\\Http\\Controllers\\Admin\\TasksController@updateduedate',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::AhGCk6HWAObxkXMG',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::lzPTnU2HrfdUWyb4' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/task/change_assignee',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\TasksController@change_assignee',
        'controller' => 'App\\Http\\Controllers\\Admin\\TasksController@change_assignee',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::lzPTnU2HrfdUWyb4',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.invoice.general-invoice' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/invoice/general-invoice',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\InvoiceController@general_invoice',
        'controller' => 'App\\Http\\Controllers\\Admin\\InvoiceController@general_invoice',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.invoice.general-invoice',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.applications.detail' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/applications/detail/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@detail',
        'controller' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@detail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.applications.detail',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::uieRT1SUKU0p5Ayu' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/interested-service',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@interestedService',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@interestedService',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::uieRT1SUKU0p5Ayu',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::bvVzFha8XD24HkPk' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/edit-interested-service',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@editinterestedService',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@editinterestedService',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::bvVzFha8XD24HkPk',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::G8mRs7AK1BypeZPG' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/get-services',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@getServices',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@getServices',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::G8mRs7AK1BypeZPG',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::qYcTphXggFQ1XF2V' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/showproductfeeserv',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@showproductfeeserv',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@showproductfeeserv',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::qYcTphXggFQ1XF2V',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::YT6DiktuW3ZZsXB1' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/servicesavefee',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@servicesavefee',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@servicesavefee',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::YT6DiktuW3ZZsXB1',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::S5MKlVbMAlByqbFM' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/upload-mail',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@uploadmail',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@uploadmail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::S5MKlVbMAlByqbFM',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::4GhwF0YAXXibY12S' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/updatefollowupschedule',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@updatefollowupschedule',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@updatefollowupschedule',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::4GhwF0YAXXibY12S',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::RK2SGkj4HgnsMTbP' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/pinnote',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@pinnote',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@pinnote',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::RK2SGkj4HgnsMTbP',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::kPgBjYmdVFaXcX51' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/pinactivitylog',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@pinactivitylog',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@pinactivitylog',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::kPgBjYmdVFaXcX51',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::DDxODIO61wwCuOfa' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/getintrestedservice',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@getintrestedservice',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@getintrestedservice',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::DDxODIO61wwCuOfa',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::obeliaRyI23kyCsZ' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/application/saleforcastservice',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@saleforcastservice',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@saleforcastservice',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::obeliaRyI23kyCsZ',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::EVQLuqhIDSQM8SZD' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/getintrestedserviceedit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@getintrestedserviceedit',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@getintrestedserviceedit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::EVQLuqhIDSQM8SZD',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::YcUjmevnCRXIL1MT' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/saveeducation',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\EducationController@store',
        'controller' => 'App\\Http\\Controllers\\Admin\\EducationController@store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::YcUjmevnCRXIL1MT',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::ZmL09JpgGNJiN2ze' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/editeducation',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\EducationController@edit',
        'controller' => 'App\\Http\\Controllers\\Admin\\EducationController@edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::ZmL09JpgGNJiN2ze',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::EYqXRXxSw5lNboIg' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/get-educations',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\EducationController@geteducations',
        'controller' => 'App\\Http\\Controllers\\Admin\\EducationController@geteducations',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::EYqXRXxSw5lNboIg',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::Jh9bWBwrNmJbaaTH' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/getEducationdetail',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\EducationController@getEducationdetail',
        'controller' => 'App\\Http\\Controllers\\Admin\\EducationController@getEducationdetail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::Jh9bWBwrNmJbaaTH',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::aFdvZWKtExf3oVsy' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/delete-education',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\EducationController@deleteeducation',
        'controller' => 'App\\Http\\Controllers\\Admin\\EducationController@deleteeducation',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::aFdvZWKtExf3oVsy',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::vn74qRCMTMirW2cV' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/edit-test-scores',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\EducationController@edittestscores',
        'controller' => 'App\\Http\\Controllers\\Admin\\EducationController@edittestscores',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::vn74qRCMTMirW2cV',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::NAT74htPiRbotnJm' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/other-test-scores',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\EducationController@othertestscores',
        'controller' => 'App\\Http\\Controllers\\Admin\\EducationController@othertestscores',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::NAT74htPiRbotnJm',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::MJzEQRESkwZF6tDn' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/create-invoice',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\InvoiceController@createInvoice',
        'controller' => 'App\\Http\\Controllers\\Admin\\InvoiceController@createInvoice',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::MJzEQRESkwZF6tDn',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::qCXOOEgFRwmKZS55' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/application/invoice/{client_id}/{application}/{invoice_type}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\InvoiceController@getInvoice',
        'controller' => 'App\\Http\\Controllers\\Admin\\InvoiceController@getInvoice',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::qCXOOEgFRwmKZS55',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::9oNfjakHlnexbMDi' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/invoice/view/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\InvoiceController@show',
        'controller' => 'App\\Http\\Controllers\\Admin\\InvoiceController@show',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::9oNfjakHlnexbMDi',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::4Oen46w6pmBLl5nv' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/invoice/preview/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\InvoiceController@getinvoicespdf',
        'controller' => 'App\\Http\\Controllers\\Admin\\InvoiceController@getinvoicespdf',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::4Oen46w6pmBLl5nv',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::JknB5K1YSliVJZHN' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/invoice/general-store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\InvoiceController@generalStore',
        'controller' => 'App\\Http\\Controllers\\Admin\\InvoiceController@generalStore',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::JknB5K1YSliVJZHN',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::9Ty1eDQmisDigQJs' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/invoice/delete-payment',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\InvoiceController@deletepayment',
        'controller' => 'App\\Http\\Controllers\\Admin\\InvoiceController@deletepayment',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::9Ty1eDQmisDigQJs',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::GDgRxVoh8KXKWFzu' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/invoice/payment-store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\InvoiceController@invoicepaymentstore',
        'controller' => 'App\\Http\\Controllers\\Admin\\InvoiceController@invoicepaymentstore',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::GDgRxVoh8KXKWFzu',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::wt1q52YzTilo9y3t' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/get-invoices',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\InvoiceController@getinvoices',
        'controller' => 'App\\Http\\Controllers\\Admin\\InvoiceController@getinvoices',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::wt1q52YzTilo9y3t',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::C5u1Jdm5NjpUZXDS' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/get-invoices-pdf',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\InvoiceController@getinvoicespdf',
        'controller' => 'App\\Http\\Controllers\\Admin\\InvoiceController@getinvoicespdf',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::C5u1Jdm5NjpUZXDS',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::8K30mCMzLKMp3Jyg' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/delete-invoice',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\InvoiceController@deleteinvoice',
        'controller' => 'App\\Http\\Controllers\\Admin\\InvoiceController@deleteinvoice',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::8K30mCMzLKMp3Jyg',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::XmDH1AXXAozOPSko' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/invoice/general-edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\InvoiceController@updategeninvoices',
        'controller' => 'App\\Http\\Controllers\\Admin\\InvoiceController@updategeninvoices',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::XmDH1AXXAozOPSko',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::Qj2N3TPXp6uYipIz' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/invoice/com-store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\InvoiceController@updatecominvoices',
        'controller' => 'App\\Http\\Controllers\\Admin\\InvoiceController@updatecominvoices',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::Qj2N3TPXp6uYipIz',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.invoice.paid' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/invoice/paid',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\InvoiceController@paid',
        'controller' => 'App\\Http\\Controllers\\Admin\\InvoiceController@paid',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.invoice.paid',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.invoice.unpaid' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/invoice/unpaid',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\InvoiceController@unpaid',
        'controller' => 'App\\Http\\Controllers\\Admin\\InvoiceController@unpaid',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.invoice.unpaid',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.invoice.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/invoice',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\InvoiceController@index',
        'controller' => 'App\\Http\\Controllers\\Admin\\InvoiceController@index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.invoice.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::qBXnfsx0UdSqU4jn' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/payment/view/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AccountController@preview',
        'controller' => 'App\\Http\\Controllers\\Admin\\AccountController@preview',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::qBXnfsx0UdSqU4jn',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::zX5RFWLEvrU4UHri' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/getapplicationdetail',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@getapplicationdetail',
        'controller' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@getapplicationdetail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::zX5RFWLEvrU4UHri',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::ktWQfjnNKajmv5xn' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/updatestage',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@updatestage',
        'controller' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@updatestage',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::ktWQfjnNKajmv5xn',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::ordxr52WsrBZb0cB' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/completestage',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@completestage',
        'controller' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@completestage',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::ordxr52WsrBZb0cB',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::8bBGSGxaKVXKQ9XG' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/updatebackstage',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@updatebackstage',
        'controller' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@updatebackstage',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::8bBGSGxaKVXKQ9XG',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::kEMpBwzz7nUSZ9nJ' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/get-applications-logs',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@getapplicationslogs',
        'controller' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@getapplicationslogs',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::kEMpBwzz7nUSZ9nJ',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::EeNLLLNg1a0GZSho' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/get-applications',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@getapplications',
        'controller' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@getapplications',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::EeNLLLNg1a0GZSho',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::GrUG5UwtnoDRTNgP' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/create-app-note',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@addNote',
        'controller' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@addNote',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::GrUG5UwtnoDRTNgP',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::PYsjLVqcvKp29coG' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/getapplicationnotes',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@getapplicationnotes',
        'controller' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@getapplicationnotes',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::PYsjLVqcvKp29coG',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::W5SvCLVv17ACcKrF' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/application-sendmail',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@applicationsendmail',
        'controller' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@applicationsendmail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::W5SvCLVv17ACcKrF',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::5vLt6oFtTcb6CrIY' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/application/updateintake',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@updateintake',
        'controller' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@updateintake',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::5vLt6oFtTcb6CrIY',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::cjwtnqKtJ3Dfnbeg' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/application/updatedates',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@updatedates',
        'controller' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@updatedates',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::cjwtnqKtJ3Dfnbeg',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::e0lepIw3Zo2kqN9p' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/application/updateexpectwin',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@updateexpectwin',
        'controller' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@updateexpectwin',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::e0lepIw3Zo2kqN9p',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::ClPbnQ8xeOnnzrYg' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/application/getapplicationbycid',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@getapplicationbycid',
        'controller' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@getapplicationbycid',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::ClPbnQ8xeOnnzrYg',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::XKPQHg7B7hoIN1p9' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/application/spagent_application',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@spagent_application',
        'controller' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@spagent_application',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::XKPQHg7B7hoIN1p9',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::b2JrA6aU8hq1bWYW' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/application/sbagent_application',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@sbagent_application',
        'controller' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@sbagent_application',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::b2JrA6aU8hq1bWYW',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::vtiHOqG3tsP4zlzs' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/application/application_ownership',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@application_ownership',
        'controller' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@application_ownership',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::vtiHOqG3tsP4zlzs',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::8s9EO3eKsTaMJ5hT' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/application/saleforcast',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@saleforcast',
        'controller' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@saleforcast',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::8s9EO3eKsTaMJ5hT',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::NKwxV3kj79juEgkA' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/superagent',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@superagent',
        'controller' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@superagent',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::NKwxV3kj79juEgkA',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::aV5PObj7L3CLaemR' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/subagent',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@subagent',
        'controller' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@subagent',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::aV5PObj7L3CLaemR',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::q5rnIRRrqYNYzzlX' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/showproductfee',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@showproductfee',
        'controller' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@showproductfee',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::q5rnIRRrqYNYzzlX',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::SvCTg1RZVSQKuiR4' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/applicationsavefee',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@applicationsavefee',
        'controller' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@applicationsavefee',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::SvCTg1RZVSQKuiR4',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::9JGVeJHBxVIxF560' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/application/export/pdf/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@exportapplicationpdf',
        'controller' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@exportapplicationpdf',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::9JGVeJHBxVIxF560',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::RKsDlNawXgueRJKH' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/add-checklists',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@addchecklists',
        'controller' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@addchecklists',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::RKsDlNawXgueRJKH',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::cH6q9wHczqZ6zfyS' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/application/checklistupload',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@checklistupload',
        'controller' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@checklistupload',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::cH6q9wHczqZ6zfyS',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::5MJdgqvyTwgLMl9V' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/deleteapplicationdocs',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@deleteapplicationdocs',
        'controller' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@deleteapplicationdocs',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::5MJdgqvyTwgLMl9V',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::8oSvzn1X3qKxWhsz' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/application/publishdoc',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@publishdoc',
        'controller' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@publishdoc',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::8oSvzn1X3qKxWhsz',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.account.payment' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/payment',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AccountController@payment',
        'controller' => 'App\\Http\\Controllers\\Admin\\AccountController@payment',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.account.payment',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.account.payableunpaid' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/income-sharing/payables/unpaid',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AccountController@payableunpaid',
        'controller' => 'App\\Http\\Controllers\\Admin\\AccountController@payableunpaid',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.account.payableunpaid',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.account.payablepaid' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/income-sharing/payables/paid',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AccountController@payablepaid',
        'controller' => 'App\\Http\\Controllers\\Admin\\AccountController@payablepaid',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.account.payablepaid',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::WbFJGqzdTtM71Xzt' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/income-payment-store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AccountController@incomepaymentstore',
        'controller' => 'App\\Http\\Controllers\\Admin\\AccountController@incomepaymentstore',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::WbFJGqzdTtM71Xzt',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::euc0WZ1WMpZJjtOw' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/revert-payment',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AccountController@revertpayment',
        'controller' => 'App\\Http\\Controllers\\Admin\\AccountController@revertpayment',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::euc0WZ1WMpZJjtOw',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.account.receivableunpaid' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/income-sharing/receivables/unpaid',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AccountController@receivableunpaid',
        'controller' => 'App\\Http\\Controllers\\Admin\\AccountController@receivableunpaid',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.account.receivableunpaid',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.account.receivablepaid' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/income-sharing/receivables/paid',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AccountController@receivablepaid',
        'controller' => 'App\\Http\\Controllers\\Admin\\AccountController@receivablepaid',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.account.receivablepaid',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.invoice.unpaidgroupinvoice' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/group-invoice/unpaid',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\InvoiceController@unpaidgroupinvoice',
        'controller' => 'App\\Http\\Controllers\\Admin\\InvoiceController@unpaidgroupinvoice',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.invoice.unpaidgroupinvoice',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.invoice.paidgroupinvoice' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/group-invoice/paid',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\InvoiceController@paidgroupinvoice',
        'controller' => 'App\\Http\\Controllers\\Admin\\InvoiceController@paidgroupinvoice',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.invoice.paidgroupinvoice',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.invoice.creategroupinvoice' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/group-invoice/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\InvoiceController@creategroupinvoice',
        'controller' => 'App\\Http\\Controllers\\Admin\\InvoiceController@creategroupinvoice',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.invoice.creategroupinvoice',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.invoice.invoiceschedules' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/invoice-schedules',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\InvoiceController@invoiceschedules',
        'controller' => 'App\\Http\\Controllers\\Admin\\InvoiceController@invoiceschedules',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.invoice.invoiceschedules',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.invoice.paymentschedule' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/paymentschedule',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\InvoiceController@paymentschedule',
        'controller' => 'App\\Http\\Controllers\\Admin\\InvoiceController@paymentschedule',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.invoice.paymentschedule',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::yYswITU8KhaijCzD' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/setup-paymentschedule',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\InvoiceController@setuppaymentschedule',
        'controller' => 'App\\Http\\Controllers\\Admin\\InvoiceController@setuppaymentschedule',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::yYswITU8KhaijCzD',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.invoice.editpaymentschedule' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/editpaymentschedule',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\InvoiceController@editpaymentschedule',
        'controller' => 'App\\Http\\Controllers\\Admin\\InvoiceController@editpaymentschedule',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.invoice.editpaymentschedule',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::PO8svuMJ0feQdLlr' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/scheduleinvoicedetail',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\InvoiceController@scheduleinvoicedetail',
        'controller' => 'App\\Http\\Controllers\\Admin\\InvoiceController@scheduleinvoicedetail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::PO8svuMJ0feQdLlr',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::kyEvXtBo7TZVt5Xm' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/addscheduleinvoicedetail',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\InvoiceController@addscheduleinvoicedetail',
        'controller' => 'App\\Http\\Controllers\\Admin\\InvoiceController@addscheduleinvoicedetail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::kyEvXtBo7TZVt5Xm',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::0TAX9vDrcR6D0WR4' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/get-all-paymentschedules',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\InvoiceController@getallpaymentschedules',
        'controller' => 'App\\Http\\Controllers\\Admin\\InvoiceController@getallpaymentschedules',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::0TAX9vDrcR6D0WR4',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::ZbXuAcxefm6HQLp1' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/deletepaymentschedule',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\InvoiceController@deletepaymentschedule',
        'controller' => 'App\\Http\\Controllers\\Admin\\InvoiceController@deletepaymentschedule',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::ZbXuAcxefm6HQLp1',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::TCDeqsrILSKUMl0u' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/applications/preview-schedules/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\InvoiceController@apppreviewschedules',
        'controller' => 'App\\Http\\Controllers\\Admin\\InvoiceController@apppreviewschedules',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::TCDeqsrILSKUMl0u',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.feature.producttype.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/product-type',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ProductTypeController@index',
        'controller' => 'App\\Http\\Controllers\\Admin\\ProductTypeController@index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.feature.producttype.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.feature.producttype.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/product-type/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ProductTypeController@create',
        'controller' => 'App\\Http\\Controllers\\Admin\\ProductTypeController@create',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.feature.producttype.create',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.feature.producttype.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/product-type/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ProductTypeController@store',
        'controller' => 'App\\Http\\Controllers\\Admin\\ProductTypeController@store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.feature.producttype.store',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.feature.producttype.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/product-type/edit/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ProductTypeController@edit',
        'controller' => 'App\\Http\\Controllers\\Admin\\ProductTypeController@edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.feature.producttype.edit',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::J3lnDSBdlCkHfMkf' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/product-type/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ProductTypeController@edit',
        'controller' => 'App\\Http\\Controllers\\Admin\\ProductTypeController@edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::J3lnDSBdlCkHfMkf',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.feature.profiles.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/profiles',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ProfileController@index',
        'controller' => 'App\\Http\\Controllers\\Admin\\ProfileController@index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.feature.profiles.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.feature.profiles.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/profiles/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ProfileController@create',
        'controller' => 'App\\Http\\Controllers\\Admin\\ProfileController@create',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.feature.profiles.create',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.feature.profiles.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/profiles/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ProfileController@store',
        'controller' => 'App\\Http\\Controllers\\Admin\\ProfileController@store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.feature.profiles.store',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.feature.profiles.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/profiles/edit/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ProfileController@edit',
        'controller' => 'App\\Http\\Controllers\\Admin\\ProfileController@edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.feature.profiles.edit',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::Aua2GOugKBxopme5' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/profiles/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ProfileController@edit',
        'controller' => 'App\\Http\\Controllers\\Admin\\ProfileController@edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::Aua2GOugKBxopme5',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.feature.partnertype.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/partner-type',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\PartnerTypeController@index',
        'controller' => 'App\\Http\\Controllers\\Admin\\PartnerTypeController@index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.feature.partnertype.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.feature.partnertype.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/partner-type/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\PartnerTypeController@create',
        'controller' => 'App\\Http\\Controllers\\Admin\\PartnerTypeController@create',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.feature.partnertype.create',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.feature.partnertype.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/partner-type/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\PartnerTypeController@store',
        'controller' => 'App\\Http\\Controllers\\Admin\\PartnerTypeController@store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.feature.partnertype.store',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.feature.partnertype.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/partner-type/edit/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\PartnerTypeController@edit',
        'controller' => 'App\\Http\\Controllers\\Admin\\PartnerTypeController@edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.feature.partnertype.edit',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::FocBuRzuXqgdc3FO' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/partner-type/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\PartnerTypeController@edit',
        'controller' => 'App\\Http\\Controllers\\Admin\\PartnerTypeController@edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::FocBuRzuXqgdc3FO',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.feature.visatype.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/visa-type',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\VisaTypeController@index',
        'controller' => 'App\\Http\\Controllers\\Admin\\VisaTypeController@index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.feature.visatype.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.feature.visatype.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/visa-type/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\VisaTypeController@create',
        'controller' => 'App\\Http\\Controllers\\Admin\\VisaTypeController@create',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.feature.visatype.create',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.feature.visatype.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/visa-type/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\VisaTypeController@store',
        'controller' => 'App\\Http\\Controllers\\Admin\\VisaTypeController@store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.feature.visatype.store',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.feature.visatype.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/visa-type/edit/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\VisaTypeController@edit',
        'controller' => 'App\\Http\\Controllers\\Admin\\VisaTypeController@edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.feature.visatype.edit',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::T7hePriq50EhlbXH' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/visa-type/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\VisaTypeController@edit',
        'controller' => 'App\\Http\\Controllers\\Admin\\VisaTypeController@edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::T7hePriq50EhlbXH',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.feature.mastercategory.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/master-category',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\MasterCategoryController@index',
        'controller' => 'App\\Http\\Controllers\\Admin\\MasterCategoryController@index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.feature.mastercategory.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.feature.mastercategory.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/master-category/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\MasterCategoryController@create',
        'controller' => 'App\\Http\\Controllers\\Admin\\MasterCategoryController@create',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.feature.mastercategory.create',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.feature.mastercategory.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/master-category/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\MasterCategoryController@store',
        'controller' => 'App\\Http\\Controllers\\Admin\\MasterCategoryController@store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.feature.mastercategory.store',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.feature.mastercategory.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/master-category/edit/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\MasterCategoryController@edit',
        'controller' => 'App\\Http\\Controllers\\Admin\\MasterCategoryController@edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.feature.mastercategory.edit',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::Ooohly5YA0cyyVW5' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/master-category/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\MasterCategoryController@edit',
        'controller' => 'App\\Http\\Controllers\\Admin\\MasterCategoryController@edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::Ooohly5YA0cyyVW5',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.feature.leadservice.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/lead-service',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\LeadServiceController@index',
        'controller' => 'App\\Http\\Controllers\\Admin\\LeadServiceController@index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.feature.leadservice.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.feature.leadservice.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/lead-service/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\LeadServiceController@create',
        'controller' => 'App\\Http\\Controllers\\Admin\\LeadServiceController@create',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.feature.leadservice.create',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.feature.leadservice.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/lead-service/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\LeadServiceController@store',
        'controller' => 'App\\Http\\Controllers\\Admin\\LeadServiceController@store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.feature.leadservice.store',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.feature.leadservice.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/lead-service/edit/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\LeadServiceController@edit',
        'controller' => 'App\\Http\\Controllers\\Admin\\LeadServiceController@edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.feature.leadservice.edit',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::1N7KRxOix7YIipYp' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/lead-service/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\LeadServiceController@edit',
        'controller' => 'App\\Http\\Controllers\\Admin\\LeadServiceController@edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::1N7KRxOix7YIipYp',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.feature.tax.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/tax',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\TaxController@index',
        'controller' => 'App\\Http\\Controllers\\Admin\\TaxController@index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.feature.tax.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.feature.tax.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/tax/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\TaxController@create',
        'controller' => 'App\\Http\\Controllers\\Admin\\TaxController@create',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.feature.tax.create',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.feature.tax.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/tax/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\TaxController@store',
        'controller' => 'App\\Http\\Controllers\\Admin\\TaxController@store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.feature.tax.store',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.feature.tax.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/tax/edit/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\TaxController@edit',
        'controller' => 'App\\Http\\Controllers\\Admin\\TaxController@edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.feature.tax.edit',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::KzB1sVjXwoLCbsjW' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/tax/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\TaxController@edit',
        'controller' => 'App\\Http\\Controllers\\Admin\\TaxController@edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::KzB1sVjXwoLCbsjW',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.feature.subjectarea.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/subjectarea',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\SubjectAreaController@index',
        'controller' => 'App\\Http\\Controllers\\Admin\\SubjectAreaController@index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.feature.subjectarea.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.feature.subjectarea.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/subjectarea/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\SubjectAreaController@create',
        'controller' => 'App\\Http\\Controllers\\Admin\\SubjectAreaController@create',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.feature.subjectarea.create',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.feature.subjectarea.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/subjectarea/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\SubjectAreaController@store',
        'controller' => 'App\\Http\\Controllers\\Admin\\SubjectAreaController@store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.feature.subjectarea.store',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.feature.subjectarea.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/subjectarea/edit/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\SubjectAreaController@edit',
        'controller' => 'App\\Http\\Controllers\\Admin\\SubjectAreaController@edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.feature.subjectarea.edit',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::TFnksSWa17gTuLCF' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/subjectarea/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\SubjectAreaController@edit',
        'controller' => 'App\\Http\\Controllers\\Admin\\SubjectAreaController@edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::TFnksSWa17gTuLCF',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.feature.subject.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/subject',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\SubjectController@index',
        'controller' => 'App\\Http\\Controllers\\Admin\\SubjectController@index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.feature.subject.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.feature.subject.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/subject/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\SubjectController@create',
        'controller' => 'App\\Http\\Controllers\\Admin\\SubjectController@create',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.feature.subject.create',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.feature.subject.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/subject/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\SubjectController@store',
        'controller' => 'App\\Http\\Controllers\\Admin\\SubjectController@store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.feature.subject.store',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.feature.subject.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/subject/edit/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\SubjectController@edit',
        'controller' => 'App\\Http\\Controllers\\Admin\\SubjectController@edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.feature.subject.edit',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::OQalgagts0a7MqPW' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/subject/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\SubjectController@edit',
        'controller' => 'App\\Http\\Controllers\\Admin\\SubjectController@edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::OQalgagts0a7MqPW',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.feature.source.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/source',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\SourceController@index',
        'controller' => 'App\\Http\\Controllers\\Admin\\SourceController@index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.feature.source.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.feature.source.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/source/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\SourceController@create',
        'controller' => 'App\\Http\\Controllers\\Admin\\SourceController@create',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.feature.source.create',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.feature.source.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/source/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\SourceController@store',
        'controller' => 'App\\Http\\Controllers\\Admin\\SourceController@store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.feature.source.store',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.feature.source.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/source/edit/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\SourceController@edit',
        'controller' => 'App\\Http\\Controllers\\Admin\\SourceController@edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.feature.source.edit',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::wLTMdhOc4cDIq4iq' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/source/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\SourceController@edit',
        'controller' => 'App\\Http\\Controllers\\Admin\\SourceController@edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::wLTMdhOc4cDIq4iq',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.feature.tags.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/tags',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\TagController@index',
        'controller' => 'App\\Http\\Controllers\\Admin\\TagController@index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.feature.tags.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.feature.tags.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/tags/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\TagController@create',
        'controller' => 'App\\Http\\Controllers\\Admin\\TagController@create',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.feature.tags.create',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.feature.tags.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/tags/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\TagController@store',
        'controller' => 'App\\Http\\Controllers\\Admin\\TagController@store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.feature.tags.store',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.feature.tags.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/tags/edit/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\TagController@edit',
        'controller' => 'App\\Http\\Controllers\\Admin\\TagController@edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.feature.tags.edit',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::dehcoJIXo3xIxTew' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/tags/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\TagController@edit',
        'controller' => 'App\\Http\\Controllers\\Admin\\TagController@edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::dehcoJIXo3xIxTew',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.checklist.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/checklist',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ChecklistController@index',
        'controller' => 'App\\Http\\Controllers\\Admin\\ChecklistController@index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.checklist.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.checklist.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/checklist/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ChecklistController@create',
        'controller' => 'App\\Http\\Controllers\\Admin\\ChecklistController@create',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.checklist.create',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.checklist.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/checklist/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ChecklistController@store',
        'controller' => 'App\\Http\\Controllers\\Admin\\ChecklistController@store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.checklist.store',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.checklist.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/checklist/edit/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ChecklistController@edit',
        'controller' => 'App\\Http\\Controllers\\Admin\\ChecklistController@edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.checklist.edit',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.checklist.update' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/checklist/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ChecklistController@edit',
        'controller' => 'App\\Http\\Controllers\\Admin\\ChecklistController@edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.checklist.update',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.enquirysource.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/enquirysource',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\EnquirySourceController@index',
        'controller' => 'App\\Http\\Controllers\\Admin\\EnquirySourceController@index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.enquirysource.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.enquirysource.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/enquirysource/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\EnquirySourceController@create',
        'controller' => 'App\\Http\\Controllers\\Admin\\EnquirySourceController@create',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.enquirysource.create',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.enquirysource.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/enquirysource/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\EnquirySourceController@store',
        'controller' => 'App\\Http\\Controllers\\Admin\\EnquirySourceController@store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.enquirysource.store',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.enquirysource.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/enquirysource/edit/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\EnquirySourceController@edit',
        'controller' => 'App\\Http\\Controllers\\Admin\\EnquirySourceController@edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.enquirysource.edit',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.enquirysource.update' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/enquirysource/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\EnquirySourceController@edit',
        'controller' => 'App\\Http\\Controllers\\Admin\\EnquirySourceController@edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.enquirysource.update',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.feetype.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/feetype',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\FeeTypeController@index',
        'controller' => 'App\\Http\\Controllers\\Admin\\FeeTypeController@index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.feetype.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.feetype.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/feetype/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\FeeTypeController@create',
        'controller' => 'App\\Http\\Controllers\\Admin\\FeeTypeController@create',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.feetype.create',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.feetype.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/feetype/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\FeeTypeController@store',
        'controller' => 'App\\Http\\Controllers\\Admin\\FeeTypeController@store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.feetype.store',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.feetype.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/feetype/edit/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\FeeTypeController@edit',
        'controller' => 'App\\Http\\Controllers\\Admin\\FeeTypeController@edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.feetype.edit',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.feetype.update' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/feetype/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\FeeTypeController@edit',
        'controller' => 'App\\Http\\Controllers\\Admin\\FeeTypeController@edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.feetype.update',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.workflow.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/workflow',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\WorkflowController@index',
        'controller' => 'App\\Http\\Controllers\\Admin\\WorkflowController@index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.workflow.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.workflow.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/workflow/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\WorkflowController@create',
        'controller' => 'App\\Http\\Controllers\\Admin\\WorkflowController@create',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.workflow.create',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.workflow.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/workflow/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\WorkflowController@store',
        'controller' => 'App\\Http\\Controllers\\Admin\\WorkflowController@store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.workflow.store',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.workflow.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/workflow/edit/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\WorkflowController@edit',
        'controller' => 'App\\Http\\Controllers\\Admin\\WorkflowController@edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.workflow.edit',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.workflow.deactivate' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/workflow/deactivate-workflow/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\WorkflowController@deactivateWorkflow',
        'controller' => 'App\\Http\\Controllers\\Admin\\WorkflowController@deactivateWorkflow',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.workflow.deactivate',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.workflow.activate' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/workflow/activate-workflow/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\WorkflowController@activateWorkflow',
        'controller' => 'App\\Http\\Controllers\\Admin\\WorkflowController@activateWorkflow',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.workflow.activate',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.workflow.update' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/workflow/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\WorkflowController@edit',
        'controller' => 'App\\Http\\Controllers\\Admin\\WorkflowController@edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.workflow.update',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::z7ozV5Xs1a1lPY7W' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/partner/saveagreement',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\PartnersController@saveagreement',
        'controller' => 'App\\Http\\Controllers\\Admin\\PartnersController@saveagreement',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::z7ozV5Xs1a1lPY7W',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::2BYBhqlHnim7ufYP' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/partner/create-contact',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\PartnersController@createcontact',
        'controller' => 'App\\Http\\Controllers\\Admin\\PartnersController@createcontact',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::2BYBhqlHnim7ufYP',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::OwSUysa9kV8fwXQR' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/get-contacts',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\PartnersController@getcontacts',
        'controller' => 'App\\Http\\Controllers\\Admin\\PartnersController@getcontacts',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::OwSUysa9kV8fwXQR',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::lQ2dR8SgoyUyfNW3' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/deletecontact',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\PartnersController@deletecontact',
        'controller' => 'App\\Http\\Controllers\\Admin\\PartnersController@deletecontact',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::lQ2dR8SgoyUyfNW3',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::7OF3kfmcIiYvigPb' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/getcontactdetail',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\PartnersController@getcontactdetail',
        'controller' => 'App\\Http\\Controllers\\Admin\\PartnersController@getcontactdetail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::7OF3kfmcIiYvigPb',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.partners.import' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/partners-import',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\PartnersController@import',
        'controller' => 'App\\Http\\Controllers\\Admin\\PartnersController@import',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.partners.import',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::UHg7yJU6XHfgzCNh' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/partner/create-branch',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\PartnersController@createbranch',
        'controller' => 'App\\Http\\Controllers\\Admin\\PartnersController@createbranch',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::UHg7yJU6XHfgzCNh',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::AV0TDJY0Cuc4V7EK' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/get-branches',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\PartnersController@getbranch',
        'controller' => 'App\\Http\\Controllers\\Admin\\PartnersController@getbranch',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::AV0TDJY0Cuc4V7EK',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::8aupSeTL2yvDsoZe' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/getbranchdetail',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\PartnersController@getbranchdetail',
        'controller' => 'App\\Http\\Controllers\\Admin\\PartnersController@getbranchdetail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::8aupSeTL2yvDsoZe',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::TBahM3VYzCZpt3jO' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/deletebranch',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\PartnersController@deletebranch',
        'controller' => 'App\\Http\\Controllers\\Admin\\PartnersController@deletebranch',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::TBahM3VYzCZpt3jO',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::kgSCdcuBTfUNCrLx' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/saveacademic',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ProductsController@saveacademic',
        'controller' => 'App\\Http\\Controllers\\Admin\\ProductsController@saveacademic',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::kgSCdcuBTfUNCrLx',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::m4QX0N4ajsUDOpBi' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/saveotherinfo',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ProductsController@saveotherinfo',
        'controller' => 'App\\Http\\Controllers\\Admin\\ProductsController@saveotherinfo',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::m4QX0N4ajsUDOpBi',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::KfvGtDDG8fwB9TtL' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/product/getotherinfo',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ProductsController@getotherinfo',
        'controller' => 'App\\Http\\Controllers\\Admin\\ProductsController@getotherinfo',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::KfvGtDDG8fwB9TtL',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::Ey3XjHHQ94EDRQWe' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/get-all-fees',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ProductsController@getallfees',
        'controller' => 'App\\Http\\Controllers\\Admin\\ProductsController@getallfees',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::Ey3XjHHQ94EDRQWe',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::thC3YijPdM5vMJgQ' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/savefee',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ProductsController@savefee',
        'controller' => 'App\\Http\\Controllers\\Admin\\ProductsController@savefee',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::thC3YijPdM5vMJgQ',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::snqDjammUyfK7ow6' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/getfeeoptionedit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ProductsController@editfee',
        'controller' => 'App\\Http\\Controllers\\Admin\\ProductsController@editfee',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::snqDjammUyfK7ow6',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::O0CZr07e8CXrq81d' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/editfee',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ProductsController@editfeeform',
        'controller' => 'App\\Http\\Controllers\\Admin\\ProductsController@editfeeform',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::O0CZr07e8CXrq81d',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::JuB9KGR5KucePolw' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/deletefee',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ProductsController@deletefee',
        'controller' => 'App\\Http\\Controllers\\Admin\\ProductsController@deletefee',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::JuB9KGR5KucePolw',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::CQOL8fyYTaqjhRYR' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/partner/addtask',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\PartnersController@addTask',
        'controller' => 'App\\Http\\Controllers\\Admin\\PartnersController@addTask',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::CQOL8fyYTaqjhRYR',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::LqloRgmpwbwfqX0F' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/partner/get-tasks',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\PartnersController@gettasks',
        'controller' => 'App\\Http\\Controllers\\Admin\\PartnersController@gettasks',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::LqloRgmpwbwfqX0F',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::4upzuSNDXdoNLWzv' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/partner/get-task-detail',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\PartnersController@taskdetail',
        'controller' => 'App\\Http\\Controllers\\Admin\\PartnersController@taskdetail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::4upzuSNDXdoNLWzv',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::lKpeY8mwkhZ6MHno' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/partner/savecomment',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\PartnersController@savecomment',
        'controller' => 'App\\Http\\Controllers\\Admin\\PartnersController@savecomment',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::lKpeY8mwkhZ6MHno',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::5Oth5nBLL2rRiKRk' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/change-task-status',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\PartnersController@changetaskstatus',
        'controller' => 'App\\Http\\Controllers\\Admin\\PartnersController@changetaskstatus',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::5Oth5nBLL2rRiKRk',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::7gi1ceMQ9m28tVqJ' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/change-task-priority',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\PartnersController@changetaskpriority',
        'controller' => 'App\\Http\\Controllers\\Admin\\PartnersController@changetaskpriority',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::7gi1ceMQ9m28tVqJ',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::KwK1MBT4qyh4sqGN' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/promotion/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\PromotionController@store',
        'controller' => 'App\\Http\\Controllers\\Admin\\PromotionController@store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::KwK1MBT4qyh4sqGN',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::tydFW5gVLM4mVtBB' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/promotion/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\PromotionController@edit',
        'controller' => 'App\\Http\\Controllers\\Admin\\PromotionController@edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::tydFW5gVLM4mVtBB',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::7jz5h15pJ771yGSF' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/get-promotions',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\PromotionController@getpromotions',
        'controller' => 'App\\Http\\Controllers\\Admin\\PromotionController@getpromotions',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::7jz5h15pJ771yGSF',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::5JOM0CMM5rCQggpv' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/getpromotioneditform',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\PromotionController@getpromotioneditform',
        'controller' => 'App\\Http\\Controllers\\Admin\\PromotionController@getpromotioneditform',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::5JOM0CMM5rCQggpv',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::KZHuAs3Lg6uRYm3q' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/change-promotion-status',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\PromotionController@changepromotionstatus',
        'controller' => 'App\\Http\\Controllers\\Admin\\PromotionController@changepromotionstatus',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::KZHuAs3Lg6uRYm3q',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.applications.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/applications',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@index',
        'controller' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.applications.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.applications.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/applications/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@create',
        'controller' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@create',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.applications.create',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::5TYCUaw1gV1PHKJq' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/discontinue_application',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@discontinue_application',
        'controller' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@discontinue_application',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::5TYCUaw1gV1PHKJq',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::qSizMxQV0XWxWVPA' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/revert_application',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@revert_application',
        'controller' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@revert_application',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::qSizMxQV0XWxWVPA',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.applications.import' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/applications-import',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@import',
        'controller' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@import',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.applications.import',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.migration.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/migration',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@migrationindex',
        'controller' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@migrationindex',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.migration.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.officevisits.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/office-visits',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\OfficeVisitController@index',
        'controller' => 'App\\Http\\Controllers\\Admin\\OfficeVisitController@index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.officevisits.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.officevisits.waiting' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/office-visits/waiting',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\OfficeVisitController@waiting',
        'controller' => 'App\\Http\\Controllers\\Admin\\OfficeVisitController@waiting',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.officevisits.waiting',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.officevisits.attending' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/office-visits/attending',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\OfficeVisitController@attending',
        'controller' => 'App\\Http\\Controllers\\Admin\\OfficeVisitController@attending',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.officevisits.attending',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.officevisits.completed' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/office-visits/completed',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\OfficeVisitController@completed',
        'controller' => 'App\\Http\\Controllers\\Admin\\OfficeVisitController@completed',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.officevisits.completed',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.officevisits.archived' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/office-visits/archived',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\OfficeVisitController@archived',
        'controller' => 'App\\Http\\Controllers\\Admin\\OfficeVisitController@archived',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.officevisits.archived',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.officevisits.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/office-visits/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\OfficeVisitController@create',
        'controller' => 'App\\Http\\Controllers\\Admin\\OfficeVisitController@create',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.officevisits.create',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::EfrSnUCYcjHx1bxz' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/checkin',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\OfficeVisitController@checkin',
        'controller' => 'App\\Http\\Controllers\\Admin\\OfficeVisitController@checkin',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::EfrSnUCYcjHx1bxz',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::OPF2f0xwlBXIsCad' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/get-checkin-detail',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\OfficeVisitController@getcheckin',
        'controller' => 'App\\Http\\Controllers\\Admin\\OfficeVisitController@getcheckin',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::OPF2f0xwlBXIsCad',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::n6fjOBs3DuB8o726' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/update_visit_purpose',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\OfficeVisitController@update_visit_purpose',
        'controller' => 'App\\Http\\Controllers\\Admin\\OfficeVisitController@update_visit_purpose',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::n6fjOBs3DuB8o726',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::btgCzV5EugnIVR4f' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/update_visit_comment',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\OfficeVisitController@update_visit_comment',
        'controller' => 'App\\Http\\Controllers\\Admin\\OfficeVisitController@update_visit_comment',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::btgCzV5EugnIVR4f',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::Oje1Hvl6KMVp3oyx' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/attend_session',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\OfficeVisitController@attend_session',
        'controller' => 'App\\Http\\Controllers\\Admin\\OfficeVisitController@attend_session',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::Oje1Hvl6KMVp3oyx',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::ikDGoQfHv15OYx6w' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/complete_session',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\OfficeVisitController@complete_session',
        'controller' => 'App\\Http\\Controllers\\Admin\\OfficeVisitController@complete_session',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::ikDGoQfHv15OYx6w',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::1H1K7ltV1VlUavcH' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/office-visits/change_assignee',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\OfficeVisitController@change_assignee',
        'controller' => 'App\\Http\\Controllers\\Admin\\OfficeVisitController@change_assignee',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::1H1K7ltV1VlUavcH',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.enquiries.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/enquiries',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\EnquireController@index',
        'controller' => 'App\\Http\\Controllers\\Admin\\EnquireController@index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.enquiries.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.enquiries.archived' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/enquiries/archived-enquiries',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\EnquireController@Archived',
        'controller' => 'App\\Http\\Controllers\\Admin\\EnquireController@Archived',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.enquiries.archived',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::RGpJgRdWiIQHwf0t' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/enquiries/covertenquiry/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\EnquireController@covertenquiry',
        'controller' => 'App\\Http\\Controllers\\Admin\\EnquireController@covertenquiry',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::RGpJgRdWiIQHwf0t',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::NTvUWWNoKkpUCtBy' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/enquiries/archived/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\EnquireController@archivedenquiry',
        'controller' => 'App\\Http\\Controllers\\Admin\\EnquireController@archivedenquiry',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::NTvUWWNoKkpUCtBy',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.auditlogs.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/audit-logs',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AuditLogController@index',
        'controller' => 'App\\Http\\Controllers\\Admin\\AuditLogController@index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.auditlogs.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.reports.client' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/report/client',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ReportController@client',
        'controller' => 'App\\Http\\Controllers\\Admin\\ReportController@client',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.reports.client',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.reports.application' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/report/application',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ReportController@application',
        'controller' => 'App\\Http\\Controllers\\Admin\\ReportController@application',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.reports.application',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.reports.invoice' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/report/invoice',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ReportController@invoice',
        'controller' => 'App\\Http\\Controllers\\Admin\\ReportController@invoice',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.reports.invoice',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.reports.office-visit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/report/office-visit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ReportController@office_visit',
        'controller' => 'App\\Http\\Controllers\\Admin\\ReportController@office_visit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.reports.office-visit',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.reports.saleforecast-application' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/report/sale-forecast/application',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ReportController@saleforecast_application',
        'controller' => 'App\\Http\\Controllers\\Admin\\ReportController@saleforecast_application',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.reports.saleforecast-application',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.reports.interested-service' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/report/sale-forecast/interested-service',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ReportController@interested_service',
        'controller' => 'App\\Http\\Controllers\\Admin\\ReportController@interested_service',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.reports.interested-service',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.reports.personal-task-report' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/report/task/personal-task-report',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ReportController@personal_task',
        'controller' => 'App\\Http\\Controllers\\Admin\\ReportController@personal_task',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.reports.personal-task-report',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.reports.office-task-report' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/report/task/office-task-report',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ReportController@office_task',
        'controller' => 'App\\Http\\Controllers\\Admin\\ReportController@office_task',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.reports.office-task-report',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::1I2muTBskR0PP0v8' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/reports/visaexpires',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ReportController@visaexpires',
        'controller' => 'App\\Http\\Controllers\\Admin\\ReportController@visaexpires',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::1I2muTBskR0PP0v8',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::SvPsB7k89KP7aJv4' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/followup-dates',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ReportController@followupdates',
        'controller' => 'App\\Http\\Controllers\\Admin\\ReportController@followupdates',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::SvPsB7k89KP7aJv4',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::uNfb1UY2A3gPULBO' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/reports/agreementexpires',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ReportController@agreementexpires',
        'controller' => 'App\\Http\\Controllers\\Admin\\ReportController@agreementexpires',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::uNfb1UY2A3gPULBO',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.reports.noofpersonofficevisit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/report/noofpersonofficevisit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ReportController@noofpersonofficevisit',
        'controller' => 'App\\Http\\Controllers\\Admin\\ReportController@noofpersonofficevisit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.reports.noofpersonofficevisit',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.reports.clientrandomlyselectmonthly' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/report/clientrandomlyselectmonthly',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ReportController@clientrandomlyselectmonthly',
        'controller' => 'App\\Http\\Controllers\\Admin\\ReportController@clientrandomlyselectmonthly',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.reports.clientrandomlyselectmonthly',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::uVOARoPalfC34hkg' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/report/save_random_client_selection',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ReportController@saveclientrandomlyselectmonthly',
        'controller' => 'App\\Http\\Controllers\\Admin\\ReportController@saveclientrandomlyselectmonthly',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::uVOARoPalfC34hkg',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::vR2MBj2mCTXraUGx' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/save_tag',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@save_tag',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@save_tag',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::vR2MBj2mCTXraUGx',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.emails.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/emails',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\EmailController@index',
        'controller' => 'App\\Http\\Controllers\\Admin\\EmailController@index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.emails.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.emails.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/emails/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\EmailController@create',
        'controller' => 'App\\Http\\Controllers\\Admin\\EmailController@create',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.emails.create',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.emails.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/emails/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\EmailController@store',
        'controller' => 'App\\Http\\Controllers\\Admin\\EmailController@store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.emails.store',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.emails.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/emails/edit/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\EmailController@edit',
        'controller' => 'App\\Http\\Controllers\\Admin\\EmailController@edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.emails.edit',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.emails.update' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/emails/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\EmailController@edit',
        'controller' => 'App\\Http\\Controllers\\Admin\\EmailController@edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.emails.update',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.crmemailtemplate.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/crm_email_template',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\CrmEmailTemplateController@index',
        'controller' => 'App\\Http\\Controllers\\Admin\\CrmEmailTemplateController@index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.crmemailtemplate.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.crmemailtemplate.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/crm_email_template/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\CrmEmailTemplateController@create',
        'controller' => 'App\\Http\\Controllers\\Admin\\CrmEmailTemplateController@create',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.crmemailtemplate.create',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.crmemailtemplate.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/crm_email_template/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\CrmEmailTemplateController@store',
        'controller' => 'App\\Http\\Controllers\\Admin\\CrmEmailTemplateController@store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.crmemailtemplate.store',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.crmemailtemplate.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/crm_email_template/edit/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\CrmEmailTemplateController@edit',
        'controller' => 'App\\Http\\Controllers\\Admin\\CrmEmailTemplateController@edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.crmemailtemplate.edit',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.crmemailtemplate.update' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/crm_email_template/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\CrmEmailTemplateController@edit',
        'controller' => 'App\\Http\\Controllers\\Admin\\CrmEmailTemplateController@edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.crmemailtemplate.update',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.gensettings.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/gen-settings',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@gensettings',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@gensettings',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.gensettings.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.gensettings.update' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/gen-settings/update',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@gensettingsupdate',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@gensettingsupdate',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.gensettings.update',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::3sPqbPJTCvG4AbcS' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/fetch-notification',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@fetchnotification',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@fetchnotification',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::3sPqbPJTCvG4AbcS',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::xDfS8VcyFL7gpjMq' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/fetch-messages',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@fetchmessages',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@fetchmessages',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::xDfS8VcyFL7gpjMq',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.upload_checklists.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/upload-checklists',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\UploadChecklistController@index',
        'controller' => 'App\\Http\\Controllers\\Admin\\UploadChecklistController@index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.upload_checklists.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.upload_checklistsupload' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/upload-checklists/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\UploadChecklistController@store',
        'controller' => 'App\\Http\\Controllers\\Admin\\UploadChecklistController@store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.upload_checklistsupload',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.teams.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/teams',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\TeamController@index',
        'controller' => 'App\\Http\\Controllers\\Admin\\TeamController@index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.teams.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.teams.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/teams/edit/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\TeamController@edit',
        'controller' => 'App\\Http\\Controllers\\Admin\\TeamController@edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.teams.edit',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::jmRvbWTYBF4ZX6xL' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/teams/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\TeamController@edit',
        'controller' => 'App\\Http\\Controllers\\Admin\\TeamController@edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::jmRvbWTYBF4ZX6xL',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.teamsupload' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/teams/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\TeamController@store',
        'controller' => 'App\\Http\\Controllers\\Admin\\TeamController@store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.teamsupload',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::mrqf3v7fxVlqpEaD' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/all-notifications',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@allnotification',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@allnotification',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::mrqf3v7fxVlqpEaD',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'assignee.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/assignee',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'as' => 'assignee.index',
        'uses' => 'App\\Http\\Controllers\\Admin\\AssigneeController@index',
        'controller' => 'App\\Http\\Controllers\\Admin\\AssigneeController@index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'admin/',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'assignee.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/assignee/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'as' => 'assignee.create',
        'uses' => 'App\\Http\\Controllers\\Admin\\AssigneeController@create',
        'controller' => 'App\\Http\\Controllers\\Admin\\AssigneeController@create',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'admin/',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'assignee.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/assignee',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'as' => 'assignee.store',
        'uses' => 'App\\Http\\Controllers\\Admin\\AssigneeController@store',
        'controller' => 'App\\Http\\Controllers\\Admin\\AssigneeController@store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'admin/',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'assignee.show' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/assignee/{assignee}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'as' => 'assignee.show',
        'uses' => 'App\\Http\\Controllers\\Admin\\AssigneeController@show',
        'controller' => 'App\\Http\\Controllers\\Admin\\AssigneeController@show',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'admin/',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'assignee.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/assignee/{assignee}/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'as' => 'assignee.edit',
        'uses' => 'App\\Http\\Controllers\\Admin\\AssigneeController@edit',
        'controller' => 'App\\Http\\Controllers\\Admin\\AssigneeController@edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'admin/',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'assignee.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
        1 => 'PATCH',
      ),
      'uri' => 'admin/assignee/{assignee}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'as' => 'assignee.update',
        'uses' => 'App\\Http\\Controllers\\Admin\\AssigneeController@update',
        'controller' => 'App\\Http\\Controllers\\Admin\\AssigneeController@update',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'admin/',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'assignee.destroy' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'admin/assignee/{assignee}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'as' => 'assignee.destroy',
        'uses' => 'App\\Http\\Controllers\\Admin\\AssigneeController@destroy',
        'controller' => 'App\\Http\\Controllers\\Admin\\AssigneeController@destroy',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'admin/',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::csv9iLmYPvdxpj9I' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/assignee-completed',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AssigneeController@completed',
        'controller' => 'App\\Http\\Controllers\\Admin\\AssigneeController@completed',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::csv9iLmYPvdxpj9I',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::jFzc9797ftyP1gmx' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/update-task-completed',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AssigneeController@updatetaskcompleted',
        'controller' => 'App\\Http\\Controllers\\Admin\\AssigneeController@updatetaskcompleted',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::jFzc9797ftyP1gmx',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::fUX0JdT8caKpjecn' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/update-task-not-completed',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AssigneeController@updatetasknotcompleted',
        'controller' => 'App\\Http\\Controllers\\Admin\\AssigneeController@updatetasknotcompleted',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::fUX0JdT8caKpjecn',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'assignee.assigned_by_me' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/assigned_by_me',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AssigneeController@assigned_by_me',
        'controller' => 'App\\Http\\Controllers\\Admin\\AssigneeController@assigned_by_me',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'assignee.assigned_by_me',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'assignee.assigned_to_me' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/assigned_to_me',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AssigneeController@assigned_to_me',
        'controller' => 'App\\Http\\Controllers\\Admin\\AssigneeController@assigned_to_me',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'assignee.assigned_to_me',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'assignee.destroy_by_me' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'admin/destroy_by_me/{note_id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AssigneeController@destroy_by_me',
        'controller' => 'App\\Http\\Controllers\\Admin\\AssigneeController@destroy_by_me',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'assignee.destroy_by_me',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'assignee.destroy_to_me' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'admin/destroy_to_me/{note_id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AssigneeController@destroy_to_me',
        'controller' => 'App\\Http\\Controllers\\Admin\\AssigneeController@destroy_to_me',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'assignee.destroy_to_me',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'assignee.activities_completed' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/activities_completed',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AssigneeController@activities_completed',
        'controller' => 'App\\Http\\Controllers\\Admin\\AssigneeController@activities_completed',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'assignee.activities_completed',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'assignee.destroy_activity' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'admin/destroy_activity/{note_id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AssigneeController@destroy_activity',
        'controller' => 'App\\Http\\Controllers\\Admin\\AssigneeController@destroy_activity',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'assignee.destroy_activity',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'assignee.destroy_complete_activity' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'admin/destroy_complete_activity/{note_id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AssigneeController@destroy_complete_activity',
        'controller' => 'App\\Http\\Controllers\\Admin\\AssigneeController@destroy_complete_activity',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'assignee.destroy_complete_activity',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::FXGr65GNeMHXDngm' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/clients/personalfollowup/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@personalfollowup',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@personalfollowup',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::FXGr65GNeMHXDngm',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::JDfXR0295XfAhEYf' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/clients/updatefollowup/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@updatefollowup',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@updatefollowup',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::JDfXR0295XfAhEYf',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::gHER0PQ5vxDhX1Bc' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/clients/reassignfollowup/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@reassignfollowupstore',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@reassignfollowupstore',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::gHER0PQ5vxDhX1Bc',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.updatesessioncompleted' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/clients/update-session-completed',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@updatesessioncompleted',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@updatesessioncompleted',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.updatesessioncompleted',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::kW1H1WJN2nVn0TWQ' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/fetch-InPersonWaitingCount',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@fetchInPersonWaitingCount',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@fetchInPersonWaitingCount',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::kW1H1WJN2nVn0TWQ',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::jgLrF3wwzc2M3gnt' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/fetch-TotalActivityCount',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@fetchTotalActivityCount',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@fetchTotalActivityCount',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::jgLrF3wwzc2M3gnt',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'assignee.activities' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/activities',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AssigneeController@activities',
        'controller' => 'App\\Http\\Controllers\\Admin\\AssigneeController@activities',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'assignee.activities',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'activities.list' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/activities/list',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AssigneeController@getActivities',
        'controller' => 'App\\Http\\Controllers\\Admin\\AssigneeController@getActivities',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'activities.list',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::REuJAIVEISHU4iGf' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/get_assignee_list',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AssigneeController@get_assignee_list',
        'controller' => 'App\\Http\\Controllers\\Admin\\AssigneeController@get_assignee_list',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::REuJAIVEISHU4iGf',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::Q2BTdBtKJv0fBe4m' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/is_email_unique',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\LeadController@is_email_unique',
        'controller' => 'App\\Http\\Controllers\\Admin\\LeadController@is_email_unique',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::Q2BTdBtKJv0fBe4m',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::4n8eKivBej6GAhyM' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/is_contactno_unique',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\LeadController@is_contactno_unique',
        'controller' => 'App\\Http\\Controllers\\Admin\\LeadController@is_contactno_unique',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::4n8eKivBej6GAhyM',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'client.merge_records' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/merge_records',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@merge_records',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@merge_records',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'client.merge_records',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::VraGxos9s2sRH8cQ' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/clients/update-email-verified',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@updateemailverified',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@updateemailverified',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::VraGxos9s2sRH8cQ',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.feature.promocode.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/promo-code',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\PromoCodeController@index',
        'controller' => 'App\\Http\\Controllers\\Admin\\PromoCodeController@index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.feature.promocode.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.feature.promocode.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/promo-code/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\PromoCodeController@create',
        'controller' => 'App\\Http\\Controllers\\Admin\\PromoCodeController@create',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.feature.promocode.create',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.feature.promocode.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/promo-code/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\PromoCodeController@store',
        'controller' => 'App\\Http\\Controllers\\Admin\\PromoCodeController@store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.feature.promocode.store',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.feature.promocode.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/promo-code/edit/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\PromoCodeController@edit',
        'controller' => 'App\\Http\\Controllers\\Admin\\PromoCodeController@edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.feature.promocode.edit',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::FKVtLIAweLAOgWCM' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/promo-code/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\PromoCodeController@edit',
        'controller' => 'App\\Http\\Controllers\\Admin\\PromoCodeController@edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::FKVtLIAweLAOgWCM',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::ymXyGi7X8ACYY4IU' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/promo-code/checkpromocode',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\PromoCodeController@checkpromocode',
        'controller' => 'App\\Http\\Controllers\\Admin\\PromoCodeController@checkpromocode',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::ymXyGi7X8ACYY4IU',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::9EfAky4mJ6T2EQxQ' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/address_auto_populate',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@address_auto_populate',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@address_auto_populate',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::9EfAky4mJ6T2EQxQ',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::ngWIz5aogww9iQJb' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/client/createservicetaken',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@createservicetaken',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@createservicetaken',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::ngWIz5aogww9iQJb',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::aGgCLQ2aCr3xLGhi' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/client/removeservicetaken',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@removeservicetaken',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@removeservicetaken',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::aGgCLQ2aCr3xLGhi',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::xpKmmN7a7mndwjsc' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/client/getservicetaken',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@getservicetaken',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@getservicetaken',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::xpKmmN7a7mndwjsc',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::OijEhFzG1IbiBoE8' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/gettagdata',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@gettagdata',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@gettagdata',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::OijEhFzG1IbiBoE8',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.saveaccountreport' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/clients/saveaccountreport/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@saveaccountreport',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@saveaccountreport',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.saveaccountreport',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.saveaccountreport.update' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/clients/saveaccountreport',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@saveaccountreport',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@saveaccountreport',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.saveaccountreport.update',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.getTopReceiptValInDB' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/clients/getTopReceiptValInDB',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@getTopReceiptValInDB',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@getTopReceiptValInDB',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.getTopReceiptValInDB',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::4ImaMUqDbQNHaUp6' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/clients/printpreview/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@printpreview',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@printpreview',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::4ImaMUqDbQNHaUp6',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.getClientReceiptInfoById' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/clients/getClientReceiptInfoById',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@getClientReceiptInfoById',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@getClientReceiptInfoById',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.getClientReceiptInfoById',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.clientreceiptlist' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/clients/clientreceiptlist',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@clientreceiptlist',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@clientreceiptlist',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.clientreceiptlist',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'client.validate_receipt' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/validate_receipt',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@validate_receipt',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@validate_receipt',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'client.validate_receipt',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.commissionreport' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/commissionreport',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@commissionreport',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@commissionreport',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.commissionreport',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.commissionreportlist' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/commissionreport/list',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@getcommissionreport',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@getcommissionreport',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.commissionreportlist',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::NTjXNuiIjQgYDVxy' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/application/updateStudentId',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@updateStudentId',
        'controller' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@updateStudentId',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::NTjXNuiIjQgYDVxy',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::aaM2bvHI1ZmHWXgk' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/showproductfeelatest',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@showproductfeelatest',
        'controller' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@showproductfeelatest',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::aaM2bvHI1ZmHWXgk',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::K6BgF2F1NMwX5jR6' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/applicationsavefeelatest',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@applicationsavefeelatest',
        'controller' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@applicationsavefeelatest',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::K6BgF2F1NMwX5jR6',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.feature.documentchecklist.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/documentchecklist',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\DocumentChecklistController@index',
        'controller' => 'App\\Http\\Controllers\\Admin\\DocumentChecklistController@index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.feature.documentchecklist.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.feature.documentchecklist.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/documentchecklist/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\DocumentChecklistController@create',
        'controller' => 'App\\Http\\Controllers\\Admin\\DocumentChecklistController@create',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.feature.documentchecklist.create',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.feature.documentchecklist.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/documentchecklist/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\DocumentChecklistController@store',
        'controller' => 'App\\Http\\Controllers\\Admin\\DocumentChecklistController@store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.feature.documentchecklist.store',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.feature.documentchecklist.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/documentchecklist/edit/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\DocumentChecklistController@edit',
        'controller' => 'App\\Http\\Controllers\\Admin\\DocumentChecklistController@edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.feature.documentchecklist.edit',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::B5k3c0EFVU057f5b' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/documentchecklist/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\DocumentChecklistController@edit',
        'controller' => 'App\\Http\\Controllers\\Admin\\DocumentChecklistController@edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::B5k3c0EFVU057f5b',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.addalldocchecklist' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/add-alldocchecklist',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@addalldocchecklist',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@addalldocchecklist',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.addalldocchecklist',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.uploadalldocument' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/upload-alldocument',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@uploadalldocument',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@uploadalldocument',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.uploadalldocument',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.notuseddoc' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/notuseddoc',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@notuseddoc',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@notuseddoc',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.notuseddoc',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.renamechecklistdoc' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/renamechecklistdoc',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@renamechecklistdoc',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@renamechecklistdoc',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.renamechecklistdoc',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.verifydoc' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/verifydoc',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@verifydoc',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@verifydoc',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.verifydoc',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.deletealldocs' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/deletealldocs',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@deletealldocs',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@deletealldocs',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.deletealldocs',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.renamealldoc' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/renamealldoc',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@renamealldoc',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@renamealldoc',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.renamealldoc',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.backtodoc' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/backtodoc',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@backtodoc',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@backtodoc',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.backtodoc',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.partners.inactive' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/partners-inactive',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\PartnersController@inactivePartnerList',
        'controller' => 'App\\Http\\Controllers\\Admin\\PartnersController@inactivePartnerList',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.partners.inactive',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::7NNqpHSYMRsY46Tn' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/partner_change_to_inactive',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@partnerChangeToInactive',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@partnerChangeToInactive',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::7NNqpHSYMRsY46Tn',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::9F0IBw5niT4vHjmR' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/partner_change_to_active',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@partnerChangeToActive',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@partnerChangeToActive',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::9F0IBw5niT4vHjmR',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.partners.savepartnerstudentinvoice' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/partners/savepartnerstudentinvoice/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\PartnersController@savepartnerstudentinvoice',
        'controller' => 'App\\Http\\Controllers\\Admin\\PartnersController@savepartnerstudentinvoice',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.partners.savepartnerstudentinvoice',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.partners.savepartnerstudentinvoice.update' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/partners/savepartnerstudentinvoice',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\PartnersController@savepartnerstudentinvoice',
        'controller' => 'App\\Http\\Controllers\\Admin\\PartnersController@savepartnerstudentinvoice',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.partners.savepartnerstudentinvoice.update',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.partners.getTopReceiptValInDB' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/partners/getTopReceiptValInDB',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\PartnersController@getTopReceiptValInDB',
        'controller' => 'App\\Http\\Controllers\\Admin\\PartnersController@getTopReceiptValInDB',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.partners.getTopReceiptValInDB',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.partners.getEnrolledStudentList' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/partners/getEnrolledStudentList',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\PartnersController@getEnrolledStudentList',
        'controller' => 'App\\Http\\Controllers\\Admin\\PartnersController@getEnrolledStudentList',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.partners.getEnrolledStudentList',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.partners.savepartnerrecordinvoice' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/partners/savepartnerrecordinvoice/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\PartnersController@savepartnerrecordinvoice',
        'controller' => 'App\\Http\\Controllers\\Admin\\PartnersController@savepartnerrecordinvoice',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.partners.savepartnerrecordinvoice',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.partners.savepartnerrecordinvoice.update' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/partners/savepartnerrecordinvoice',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\PartnersController@savepartnerrecordinvoice',
        'controller' => 'App\\Http\\Controllers\\Admin\\PartnersController@savepartnerrecordinvoice',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.partners.savepartnerrecordinvoice.update',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.partners.savepartnerrecordpayment' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/partners/savepartnerrecordpayment/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\PartnersController@savepartnerrecordpayment',
        'controller' => 'App\\Http\\Controllers\\Admin\\PartnersController@savepartnerrecordpayment',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.partners.savepartnerrecordpayment',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.partners.savepartnerrecordpayment.update' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/partners/savepartnerrecordpayment',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\PartnersController@savepartnerrecordpayment',
        'controller' => 'App\\Http\\Controllers\\Admin\\PartnersController@savepartnerrecordpayment',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.partners.savepartnerrecordpayment.update',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.partners.getRecordedInvoiceList' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/partners/getRecordedInvoiceList',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\PartnersController@getRecordedInvoiceList',
        'controller' => 'App\\Http\\Controllers\\Admin\\PartnersController@getRecordedInvoiceList',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.partners.getRecordedInvoiceList',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.partners.updateStudentStatus' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/partners/update-student-status',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\PartnersController@updateStudentStatus',
        'controller' => 'App\\Http\\Controllers\\Admin\\PartnersController@updateStudentStatus',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.partners.updateStudentStatus',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.partners.getStudentInfo' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/partners/getStudentInfo',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\PartnersController@getStudentInfo',
        'controller' => 'App\\Http\\Controllers\\Admin\\PartnersController@getStudentInfo',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.partners.getStudentInfo',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.partners.getStudentCourseInfo' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/partners/getStudentCourseInfo',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\PartnersController@getStudentCourseInfo',
        'controller' => 'App\\Http\\Controllers\\Admin\\PartnersController@getStudentCourseInfo',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.partners.getStudentCourseInfo',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.partners.getTopInvoiceValInDB' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/partners/getTopInvoiceValInDB',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\PartnersController@getTopInvoiceValInDB',
        'controller' => 'App\\Http\\Controllers\\Admin\\PartnersController@getTopInvoiceValInDB',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.partners.getTopInvoiceValInDB',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::HIWsowVbao20uVV4' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/partners/printpreviewcreateinvoice/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\PartnersController@printpreviewcreateinvoice',
        'controller' => 'App\\Http\\Controllers\\Admin\\PartnersController@printpreviewcreateinvoice',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::HIWsowVbao20uVV4',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.partners.updateInvoiceSentOptionToYes' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/partners/updateInvoiceSentOptionToYes',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\PartnersController@updateInvoiceSentOptionToYes',
        'controller' => 'App\\Http\\Controllers\\Admin\\PartnersController@updateInvoiceSentOptionToYes',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.partners.updateInvoiceSentOptionToYes',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.partners.getInfoByInvoiceId' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/partners/getInfoByInvoiceId',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\PartnersController@getInfoByInvoiceId',
        'controller' => 'App\\Http\\Controllers\\Admin\\PartnersController@getInfoByInvoiceId',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.partners.getInfoByInvoiceId',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.partners.getEnrolledStudentListInEditMode' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/partners/getEnrolledStudentListInEditMode',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\PartnersController@getEnrolledStudentListInEditMode',
        'controller' => 'App\\Http\\Controllers\\Admin\\PartnersController@getEnrolledStudentListInEditMode',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.partners.getEnrolledStudentListInEditMode',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.partners.deleteStudentRecordByInvoiceId' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/partners/deleteStudentRecordByInvoiceId',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\PartnersController@deleteStudentRecordByInvoiceId',
        'controller' => 'App\\Http\\Controllers\\Admin\\PartnersController@deleteStudentRecordByInvoiceId',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.partners.deleteStudentRecordByInvoiceId',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.partners.deleteStudentRecordInvoiceByInvoiceId' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/partners/deleteStudentRecordInvoiceByInvoiceId',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\PartnersController@deleteStudentRecordInvoiceByInvoiceId',
        'controller' => 'App\\Http\\Controllers\\Admin\\PartnersController@deleteStudentRecordInvoiceByInvoiceId',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.partners.deleteStudentRecordInvoiceByInvoiceId',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.partners.deleteStudentPaymentInvoiceByInvoiceId' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/partners/deleteStudentPaymentInvoiceByInvoiceId',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\PartnersController@deleteStudentPaymentInvoiceByInvoiceId',
        'controller' => 'App\\Http\\Controllers\\Admin\\PartnersController@deleteStudentPaymentInvoiceByInvoiceId',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.partners.deleteStudentPaymentInvoiceByInvoiceId',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::NdzqjHEQJ1Ci0BT0' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/upload-partner-fetch-mail',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\PartnersController@uploadpartnerfetchmail',
        'controller' => 'App\\Http\\Controllers\\Admin\\PartnersController@uploadpartnerfetchmail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::NdzqjHEQJ1Ci0BT0',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::CaRGga7SwyQvnuGD' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/upload-partner-sent-fetch-mail',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\PartnersController@uploadpartnersentfetchmail',
        'controller' => 'App\\Http\\Controllers\\Admin\\PartnersController@uploadpartnersentfetchmail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::CaRGga7SwyQvnuGD',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.applications.overdue' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/applications-overdue',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@overdueApplicationList',
        'controller' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@overdueApplicationList',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.applications.overdue',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.applications.finalize' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/applications-finalize',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@finalizeApplicationList',
        'controller' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@finalizeApplicationList',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.applications.finalize',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::QYxHEiKaXgEgDUeq' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/partners/followup_partner/store_partner',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\PartnersController@followupstore_partner',
        'controller' => 'App\\Http\\Controllers\\Admin\\PartnersController@followupstore_partner',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::QYxHEiKaXgEgDUeq',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::e2sQNt4Rs99h3OL2' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/clients/fetchClientContactNo',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@fetchClientContactNo',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@fetchClientContactNo',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::e2sQNt4Rs99h3OL2',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::8f9hlEsTCCLHEeoL' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/partners/fetchPartnerContactNo',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\PartnersController@fetchPartnerContactNo',
        'controller' => 'App\\Http\\Controllers\\Admin\\PartnersController@fetchPartnerContactNo',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::8f9hlEsTCCLHEeoL',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.partners.updateStudentApplicationOverallStatus' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/partners/update-student-application-overall-status',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\PartnersController@updateStudentApplicationOverallStatus',
        'controller' => 'App\\Http\\Controllers\\Admin\\PartnersController@updateStudentApplicationOverallStatus',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.partners.updateStudentApplicationOverallStatus',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.partners.addstudentnote' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/add-student-note',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\PartnersController@addstudentnote',
        'controller' => 'App\\Http\\Controllers\\Admin\\PartnersController@addstudentnote',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.partners.addstudentnote',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.partners.activities' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/get-partner-activities',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\PartnersController@activities',
        'controller' => 'App\\Http\\Controllers\\Admin\\PartnersController@activities',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.partners.activities',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.partners.updatecommissionpercentage' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/partners/updatecommissionpercentage/{partner_id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\PartnersController@updatecommissionpercentage',
        'controller' => 'App\\Http\\Controllers\\Admin\\PartnersController@updatecommissionpercentage',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.partners.updatecommissionpercentage',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.partners.updatecommissionclaimed' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/partners/updatecommissionclaimed/{partner_id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\PartnersController@updatecommissionclaimed',
        'controller' => 'App\\Http\\Controllers\\Admin\\PartnersController@updatecommissionclaimed',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.partners.updatecommissionclaimed',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::1RSQkGKYGsOTvXcN' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/update-note-deadline-completed',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@updatenotedeadlinecompleted',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@updatenotedeadlinecompleted',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::1RSQkGKYGsOTvXcN',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::kw7qYt6QRjXMztif' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/extenddeadlinedate',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@extenddeadlinedate',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@extenddeadlinedate',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::kw7qYt6QRjXMztif',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::ltGa8jEHxejPiTrp' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/refund_application',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@refund_application',
        'controller' => 'App\\Http\\Controllers\\Admin\\ApplicationsController@refund_application',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::ltGa8jEHxejPiTrp',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.partners.saveStudentNote' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/partners/save-student-note',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\PartnersController@saveStudentNote',
        'controller' => 'App\\Http\\Controllers\\Admin\\PartnersController@saveStudentNote',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.partners.saveStudentNote',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.partners.getPartnerNotes' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/get-partner-notes',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\PartnersController@getPartnerNotes',
        'controller' => 'App\\Http\\Controllers\\Admin\\PartnersController@getPartnerNotes',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.partners.getPartnerNotes',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.sendmsg' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/sendmsg',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@sendmsg',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@sendmsg',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.sendmsg',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'verify.is-phone-verify-or-not' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/verify/is-phone-verify-or-not',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\SMSTwilioController@isPhoneVerifyOrNot',
        'controller' => 'App\\Http\\Controllers\\Admin\\SMSTwilioController@isPhoneVerifyOrNot',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'verify.is-phone-verify-or-not',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'verify.send-code' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/verify/send-code',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\SMSTwilioController@sendVerificationCode',
        'controller' => 'App\\Http\\Controllers\\Admin\\SMSTwilioController@sendVerificationCode',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'verify.send-code',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'verify.check-code' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/verify/check-code',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\SMSTwilioController@verifyCode',
        'controller' => 'App\\Http\\Controllers\\Admin\\SMSTwilioController@verifyCode',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'verify.check-code',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'sms.form' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/sms',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\SmsController@showForm',
        'controller' => 'App\\Http\\Controllers\\Admin\\SmsController@showForm',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'sms.form',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'sms.send' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/sms',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\SmsController@send',
        'controller' => 'App\\Http\\Controllers\\Admin\\SmsController@send',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'sms.send',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'sms.status' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/sms/status/{messageId}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\SmsController@checkStatus',
        'controller' => 'App\\Http\\Controllers\\Admin\\SmsController@checkStatus',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'sms.status',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'sms.responses' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/sms/responses',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\SmsController@getResponses',
        'controller' => 'App\\Http\\Controllers\\Admin\\SmsController@getResponses',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'sms.responses',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.clients.isgreviewmailsent' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/is_greview_mail_sent',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@isgreviewmailsent',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@isgreviewmailsent',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.clients.isgreviewmailsent',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.mail.enhance' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/mail/enhance',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@enhanceMessage',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@enhanceMessage',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'admin.mail.enhance',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::QoBeQEfJfTiAOaRX' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/upload-partner-document-upload',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\PartnersController@uploadpartnerdocumentupload',
        'controller' => 'App\\Http\\Controllers\\Admin\\PartnersController@uploadpartnerdocumentupload',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::QoBeQEfJfTiAOaRX',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::rvUlCxkV2jsZqf7C' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/download-document',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ClientsController@download_document',
        'controller' => 'App\\Http\\Controllers\\Admin\\ClientsController@download_document',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/admin',
        'where' => 
        array (
        ),
        'as' => 'generated::rvUlCxkV2jsZqf7C',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'emailVerify' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'email-verify',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\HomeController@emailVerify',
        'controller' => 'App\\Http\\Controllers\\HomeController@emailVerify',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'emailVerify',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'emailVerifyToken' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'email-verify-token/{token}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\HomeController@emailVerifyToken',
        'controller' => 'App\\Http\\Controllers\\HomeController@emailVerifyToken',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'emailVerifyToken',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::6tIo0m239VsceOIK' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'verify-dob/{encoded_id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\HomeController@showDobForm',
        'controller' => 'App\\Http\\Controllers\\HomeController@showDobForm',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::6tIo0m239VsceOIK',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::QwZoCRiDCJp5hbzr' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'verify-dob',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\HomeController@verifyDob',
        'controller' => 'App\\Http\\Controllers\\HomeController@verifyDob',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::QwZoCRiDCJp5hbzr',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::5JyGFXV0XtVm9cz0' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'editclient/{encoded_id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'checkDobSession',
        ),
        'uses' => 'App\\Http\\Controllers\\HomeController@editClient',
        'controller' => 'App\\Http\\Controllers\\HomeController@editClient',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::5JyGFXV0XtVm9cz0',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'editclient' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'editclient',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\HomeController@editclient',
        'controller' => 'App\\Http\\Controllers\\HomeController@editclient',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'editclient',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'home' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'home',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\HomeController@index',
        'controller' => 'App\\Http\\Controllers\\HomeController@index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'home',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::ilD4VyeVppWtweC4' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'POST',
        2 => 'HEAD',
      ),
      'uri' => 'broadcasting/auth',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => '\\Illuminate\\Broadcasting\\BroadcastController@authenticate',
        'controller' => '\\Illuminate\\Broadcasting\\BroadcastController@authenticate',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'excluded_middleware' => 
        array (
          0 => 'Illuminate\\Foundation\\Http\\Middleware\\VerifyCsrfToken',
        ),
        'as' => 'generated::ilD4VyeVppWtweC4',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
  ),
)
);
