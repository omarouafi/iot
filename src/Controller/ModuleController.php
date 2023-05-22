<?php

namespace App\Controller;

use App\Entity\Module;
use App\Form\ModuleType;
use App\Repository\FonctionementRepository;
use App\Repository\MesureRepository;
use App\Repository\ModuleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ModuleController extends AbstractController
{
    /**
     * @Route("/modules", name="modules_list", methods={"GET"})
     */
    public function index(ModuleRepository $moduleRepository): Response
    {
        $modules = $moduleRepository->findAll();

        return $this->render('module/index.html.twig', [
            'modules' => $modules,
        ]);
    }

    /**
     * @Route("/modules/create", name="module_create", methods={"POST"})
     */
    public function create(Request $request, ModuleRepository $moduleRepository): Response
    {
        try {
            $module = new Module();

            $module->setName($request->request->get('name'));
            $module->setDescription($request->request->get('description'));
            $module->setMesureType($request->request->get('type'));

            $moduleRepository->save($module, true);

            $this->addFlash('success', 'Module créé avec succès.');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Échec de la création du module : ' . $e->getMessage());
        }

        return $this->redirect("/modules");
    }

    /**
     * @Route("/modules/{id}/edit", name="module_edit_view", methods={"GET"})
     */
    public function edit_view(Module $module): Response
    {
        return $this->render('module/edit.html.twig', [
            'module' => $module,
        ]);
    }

    /**
     * @Route("/modules/{id}/edit", name="module_edit", methods={"POST"})
     */
    public function edit(Request $request, Module $module, ModuleRepository $moduleRepository): Response
    {
        try {
            $module->setName($request->request->get('name'));
            $module->setDescription($request->request->get('description'));
            $module->setMesureType($request->request->get('type'));

            $moduleRepository->save($module, true);

            $this->addFlash('success', 'Module mis à jour avec succès.');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Échec de la mise à jour du module : ' . $e->getMessage());
        }

        return $this->redirect("/modules");
    }

    /**
     * @Route("/modules/{id}/delete", name="module_delete", methods={"GET"})
     */
    public function delete(Request $request, Module $module, ModuleRepository $moduleRepository): Response
    {
        try {
            $moduleRepository->remove($module, true);

            $this->addFlash('success', 'Module supprimé avec succès.');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Échec de la suppression du module : ' . $e->getMessage());
        }

        return $this->redirect("/modules");
    }

    /**
     * @Route("/modules/{id}/details", name="module_details", methods={"GET"})
     */
    public function details(Module $module, MesureRepository $mesureRepository, FonctionementRepository $fonctionementRepository): Response
    {
        $mesures = $mesureRepository->findBy([
        'module' => $module
        ]);

        $mesuresData = [];
        foreach ($mesures as $mesure) {
            $mesuresData[] = [
                'createdAt' => $mesure->getCreatedAt()->format('Y-m-d H:i:s'),
                'value' => $mesure->getValue(),
            ];
        }

        $mesures = json_encode($mesuresData);
        
        $fonctionnements = $fonctionementRepository->findBy([
            'module' => $module
        ]);


        return $this->render('module/details.html.twig', [
            'module' => $module,
            'mesures' => $mesures,
            'fonctionnements' => $fonctionnements
        ]);
    }
    /**
        * @Route("/modules", name="modules")
    */
    public function redirectToModules(): Response
    {
        return $this->redirect('/modules');
    }
}
