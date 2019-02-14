@if($form->isLoadCityData())
@endif
@if($form->isLoadCityAreaData())
@endif
<?=$form->getSuccessScript()?>

        rule : {!! json_encode($form->getRules()) !!},
        form : {!! json_encode($form->getConfig('form')) !!},
        row : {!! json_encode($form->getConfig('row')) !!},
        action : '{{$form->getAction()}}',
        method : '{{$form->getMethod()}}'
