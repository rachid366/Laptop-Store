<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Filesystem\Filesystem;



class ProductController extends AbstractController
{
    private $productRepository;
    //entitymanager pour connecter et enregistrer dans la base de donner
    private $entityManager;
  
    public function __construct(ProductRepository $productRepository, ManagerRegistry $doctrine)
    {
     $this->productRepository = $productRepository;
     $this->entityManager = $doctrine->getManager();
    }

    
    #[Route('/product', name: 'product_list')]
    public function index(): Response
    {   
//recupirer tous les donner 
        $products = $this->productRepository->findAll();
        return $this->render('product/index.html.twig', [
            'products' => $products,
        ]);
    }

    #[Route('/store/product', name: 'product_store')]
    public function store(Request $request ): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class,$product);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
          //recupirer les donner de laform
            $product = $form->getData();
            //aploid des images
            //tester 
            if($request->files->get('product')['image']){
                //recupirer image
                $image = $request->files->get('product')['image'];
                //donner le nom de image
                $image_name = time().'_'.$image->getClientOriginalName();
                //envoyer image a dossier de service.yaml
                $image->move($this->getParameter('image_directory'),$image_name);
                //donner image o produit
                $product->setImage($image_name);
            }
            //ajouter le produit
            $this->entityManager->persist($product);
            $this->entityManager->flush();
           //ajouter message 
            $this->addFlash(
                'success',
                'your product was saved'
            );
            return $this->redirectToRoute('product_list');
        }

        return $this->renderForm('product/create.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/product/details/{id}', name: 'product_show')]
    public function show(Product $product): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product,
            'photo_url' => 'http://127.0.0.1:8000/uploads/'

        ]);
    }

    #[Route('/product/edit/{id}', name: 'product_edit')]
     public function editProduct(Product $product,Request $request): Response
    {
    
        $form = $this->createForm(ProductType::class,$product);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $product = $form->getData();
            if($request->files->get('product')['image']){
                //recupirer image
                $image = $request->files->get('product')['image'];
                //donner le nom de image
                $image_name = time().'_'.$image->getClientOriginalName();
                //envoyer image a dossier de service.yaml
                $image->move($this->getParameter('image_directory'),$image_name);
                //donner image o produit
                $product->setImage($image_name);
            }
            //ajouter le produit
            $this->entityManager->persist($product);
            $this->entityManager->flush();
           //ajouter message 
            $this->addFlash(
                'success',
                'your product was updated'
            );
            return $this->redirectToRoute('product_list');
        }

        return $this->renderForm('product/edit.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/product/delete/{id}', name: 'product_delete')]
    public function delete(Product $product): Response
    {
        //pour supprimer image 
        $filesystem = new Filesystem(); 
        //chemin de image
        $imagePath = './uploads/'.$product->getImage();
        if($filesystem->exists($imagePath)){
            $filesystem->remove($imagePath);
        }

        // tell Doctrine you want to (eventually) save the Product (no queries yet)
        $this->entityManager->remove($product);

        // actually executes the queries (i.e. the INSERT query)
        $this->entityManager->flush();

        $this->addFlash(
            'success',
            'Your product was removed'
        );
        return $this->redirectToRoute('product_list');
    }
}
