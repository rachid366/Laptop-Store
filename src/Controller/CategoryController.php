<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{

    private $categoryRepository;
    private $entityManager;
  
    public function __construct(CategoryRepository $categoryRepository, ManagerRegistry $doctrine)
    {
     $this->categoryRepository = $categoryRepository;
     $this->entityManager = $doctrine->getManager();
    }


    #[Route('/category', name: 'category_list')]
    public function index(): Response
    {
        $categorys = $this->categoryRepository->findAll();
        return $this->render('category/index.html.twig', [
            'categorys' => $categorys,
        ]);
    }

    #[Route('/store/category', name: 'Add_Category')]
    
    public function store(Request $request ): Response
    {

        $category = new Category();
        $form = $this->createForm(CategoryType::class,$category);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            //recupirer les donner de laform
              $category = $form->getData();

              $this->entityManager->persist($category);
              $this->entityManager->flush();
             //ajouter message 
              $this->addFlash(
                  'success',
                  'your category was saved'
              );
              return $this->redirectToRoute('category_list');
          }

        return $this->renderForm('category/create.html.twig', [
            'form' => $form,
        ]);
    }



    #[Route('/category/edit/{id}', name: 'category_edit')]
    public function editcategory(Category $category,Request $request): Response
   {
       $form = $this->createForm(CategoryType::class,$category);
       $form->handleRequest($request);
      
       if($form->isSubmitted() && $form->isValid()){
        //recupirer les donner de laform
          $category = $form->getData();

          $this->entityManager->persist($category);
          $this->entityManager->flush();
         //ajouter message 
          $this->addFlash(
              'success',
              'your category was updated'
          );
          return $this->redirectToRoute('category_list');
      }
       return $this->renderForm('category/edit.html.twig', [
           'form' => $form,
       ]);
   }

    #[Route('/category/delete/{id}', name: 'category_delete')]
    public function delete(category $category): Response
    {
                $this->entityManager->remove($category);
        $this->entityManager->flush();

        $this->addFlash(
            'success',
            'Your category was removed'
        );
        return $this->redirectToRoute('category_list');
    }
       
    }


