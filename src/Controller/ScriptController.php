<?php

namespace OHMedia\ScriptBundle\Controller;

use OHMedia\BackendBundle\Routing\Attribute\Admin;
use OHMedia\ScriptBundle\Entity\Script;
use OHMedia\ScriptBundle\Form\ScriptType;
use OHMedia\ScriptBundle\Repository\ScriptRepository;
use OHMedia\ScriptBundle\Security\Voter\ScriptVoter;
use OHMedia\TimezoneBundle\Service\Timezone;
use OHMedia\UtilityBundle\Form\DeleteType;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Admin]
class ScriptController extends AbstractController
{
    public function __construct(
        private ScriptRepository $scriptRepository,
        private Timezone $timezone,
    ) {
    }

    #[Route('/scripts', name: 'script_index', methods: ['GET'])]
    public function index(): Response
    {
        $newScript = new Script();

        $this->denyAccessUnlessGranted(
            ScriptVoter::INDEX,
            $newScript,
            'You cannot access the list of scripts.'
        );

        $scripts = $this->scriptRepository->createQueryBuilder('a')
            ->orderBy('a.starts_at', 'desc')
            ->getQuery()
            ->getResult();

        return $this->render('@OHMediaScript/script_index.html.twig', [
            'scripts' => $scripts,
            'new_script' => $newScript,
            'attributes' => $this->getAttributes(),
            'active_script' => $this->scriptRepository->getActive(),
        ]);
    }

    #[Route('/script/create', name: 'script_create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        $script = new Script();

        $this->denyAccessUnlessGranted(
            ScriptVoter::CREATE,
            $script,
            'You cannot create a new script.'
        );

        $form = $this->createForm(ScriptType::class, $script);

        $form->add('save', SubmitType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $this->scriptRepository->save($script, true);

                $this->addFlash('notice', 'The script was created successfully.');

                return $this->redirectToRoute('script_index');
            }

            $this->addFlash('error', 'There are some errors in the form below.');
        }

        $this->setActiveScriptFlash($script);

        return $this->render('@OHMediaScript/script_create.html.twig', [
            'form' => $form->createView(),
            'script' => $script,
        ]);
    }

    #[Route('/script/{id}/edit', name: 'script_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        #[MapEntity(id: 'id')] Script $script,
    ): Response {
        $this->denyAccessUnlessGranted(
            ScriptVoter::EDIT,
            $script,
            'You cannot edit this script.'
        );

        $form = $this->createForm(ScriptType::class, $script);

        $form->add('save', SubmitType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $this->scriptRepository->save($script, true);

                $this->addFlash('notice', 'The script was updated successfully.');

                return $this->redirectToRoute('script_index');
            }

            $this->addFlash('error', 'There are some errors in the form below.');
        }

        $this->setActiveScriptFlash($script);

        return $this->render('@OHMediaScript/script_edit.html.twig', [
            'form' => $form->createView(),
            'script' => $script,
        ]);
    }

    private function setActiveScriptFlash(Script $script)
    {
        $activeScript = $this->scriptRepository->getActive();

        $message = null;

        if ($activeScript === $script) {
            $this->addFlash('info', 'This is the current active script.');
        } elseif ($activeScript) {
            $timezone = new \DateTimeZone($this->timezone->get());
            $startsAt = $activeScript->getStartsAt()->setTimezone($timezone);

            if ($activeScript->getEndsAt()) {
                $endsAt = $activeScript->getEndsAt()->setTimezone($timezone);

                $message = sprintf(
                    'The current active script started on %s and will expire on %s.',
                    $startsAt->format('M j, Y @ g:ia'),
                    $endsAt->format('M j, Y @ g:ia'),
                );
            } else {
                $message = sprintf(
                    'The current active script started on %s and is not set to expire.',
                    $startsAt->format('M j, Y @ g:ia'),
                );
            }
        }

        if ($message) {
            $this->addFlash('info', $message);
        }
    }

    #[Route('/script/{id}/delete', name: 'script_delete', methods: ['GET', 'POST'])]
    public function delete(
        Request $request,
        #[MapEntity(id: 'id')] Script $script,
    ): Response {
        $this->denyAccessUnlessGranted(
            ScriptVoter::DELETE,
            $script,
            'You cannot delete this script.'
        );

        $form = $this->createForm(DeleteType::class, null);

        $form->add('delete', SubmitType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $this->scriptRepository->remove($script, true);

                $this->addFlash('notice', 'The script was deleted successfully.');

                return $this->redirectToRoute('script_index');
            }

            $this->addFlash('error', 'There are some errors in the form below.');
        }

        $activeScript = $this->scriptRepository->getActive();

        if ($script === $activeScript) {
            $this->addFlash('warning', 'This is the current active script.');
        }

        return $this->render('@OHMediaScript/script_delete.html.twig', [
            'form' => $form->createView(),
            'script' => $script,
        ]);
    }

    private function getAttributes(): array
    {
        return [
            'create' => ScriptVoter::CREATE,
            'delete' => ScriptVoter::DELETE,
            'edit' => ScriptVoter::EDIT,
        ];
    }
}
